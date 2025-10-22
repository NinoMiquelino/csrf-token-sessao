<?php
// Reporta erros, exceto notices (bom para debug)
error_reporting(E_ALL & ~E_NOTICE); 

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Mudar para o domínio do frontend em produção
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token'); // Adiciona X-CSRF-Token

// --- Funções de Segurança Fornecidas ---

function sec_session_start() {
    // Defina um nome de sessão complexo e único
    $session_name = md5('nome_complexo_unico_session_csrf_project'); 
    // Verifica se a conexão é segura (HTTPS)
    // Usamos $secure = false para testes em localhost HTTP
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    // Se estiver em localhost, forçamos $secure para false
    if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1') {
        $secure = false;
    }
    
    // Define o parâmetro HttpOnly
    $httponly = true;
    
    // Força as sessões a utilizarem apenas cookies
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_httponly', 1);
    
    // Obtém os parâmetros atuais dos cookies
    $cookieParams = session_get_cookie_params(); 
    $cookieParams["secure"] = $secure;
    
    // Obtém o domínio para configuração de cookies
    $domain = $_SERVER['SERVER_NAME'] ?? '';
    
    // Verifica a versão do PHP para definir os parâmetros de sessão corretamente
    if (PHP_VERSION_ID < 70300) {
        // Versões anteriores ao PHP 7.3
        session_set_cookie_params(
            $cookieParams["lifetime"],
            $cookieParams["path"],
            $domain,
            $cookieParams["secure"],
            $httponly
        );
    } else {
        // PHP 7.3 e versões mais recentes
        session_set_cookie_params([
            'lifetime' => $cookieParams["lifetime"],
            'path' => $cookieParams["path"],
            'domain' => $domain,
            'secure' => $cookieParams["secure"],
            'httponly' => $httponly,
            // Use 'Lax' ou 'Strict' em produção. 'None' é necessário para CORS/HTTPS.
            'samesite' => $secure ? 'None' : 'Lax' 
        ]);
    }
    
    // Define o nome da sessão
    session_name($session_name);
    
    // Inicia a sessão
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Regenera a ID da sessão para evitar ataques de fixação de sessão
    session_regenerate_id(true);
}


function generate_csrf_token() {
    try {
        sec_session_start();
        
        $random_bytes = random_bytes(32);    
        $client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $unique_data = hash('sha256', ($_SERVER['HTTP_USER_AGENT'] ?? '') . $client_ip, true);    
        
        // Geração de UUID simplificada (para garantir que random_int não falhe)
        $uuid = bin2hex(random_bytes(16));

        // Combina dados e gera o token final
        $token = rtrim(base64_encode(hash('sha512', bin2hex($random_bytes) . $unique_data . $uuid, true)), '=');    
        
        // Armazena o token na sessão
        $_SESSION['csrf_token'] = [
            'token' => $token,
            'expires' => time() + 3600 // 1 hora de validade
        ];
        
        return $token;
    } catch (Exception $e) {
        // Lida com falha ao gerar bytes aleatórios
        error_log("CSRF Token Generation Failed: " . $e->getMessage());
        throw new RuntimeException('Falha ao gerar CSRF token.');
    }
}

function verify_csrf_token($user_token) {
    sec_session_start();
    
    if (isset($_SESSION['csrf_token'])) {
        $csrf_token = $_SESSION['csrf_token'];
        
        // 1. Verifica se o token expirou
        if ($csrf_token['expires'] < time()) {
            unset($_SESSION['csrf_token']); // Remove token expirado
            return false;
        }
        
        // 2. Verifica se o token corresponde (uso seguro de hash_equals)
        if (hash_equals($csrf_token['token'], $user_token)) {
            unset($_SESSION['csrf_token']); // Invalida o token após a verificação
            return true;
        }
    }
    return false;
}


// --- Roteamento da API ---

$method = $_SERVER['REQUEST_METHOD'] ?? '';

if ($method === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $action = $data['action'] ?? '';

    $result = ['success' => false, 'message' => '', 'token' => '', 'error' => ''];

    try {
        switch ($action) {
            case 'create_token':
                $token = generate_csrf_token();
                $result['success'] = true;
                $result['message'] = 'Token CSRF criado com sucesso e armazenado na sessão.';
                $result['token'] = $token;
                break;

            case 'verify_token':
                // O token a ser verificado pode vir do corpo ou do cabeçalho
                $user_token = $data['user_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

                if (empty($user_token)) {
                    throw new Exception("Token para verificação não fornecido.");
                }
                
                if (verify_csrf_token($user_token)) {
                    $result['success'] = true;
                    $result['message'] = 'Token CSRF VERIFICADO com sucesso! (Token invalidado).';
                } else {
                    // Retornar 403 (Forbidden) é uma boa prática de segurança
                    http_response_code(403);
                    $result['error'] = 'Falha na verificação do Token CSRF: inválido ou expirado.';
                }
                break;
                
            default:
                throw new Exception('Ação inválida.');
        }
    } catch (RuntimeException $e) {
         http_response_code(500);
         $result['error'] = 'Erro de Servidor: ' . $e->getMessage();
    } catch (Exception $e) {
        http_response_code(400);
        $result['error'] = $e->getMessage();
    }

    echo json_encode($result);
    
} elseif ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido.']);
}
?>
