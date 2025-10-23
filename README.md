## 👨‍💻 Autor

<div align="center">
  <img src="https://avatars.githubusercontent.com/ninomiquelino" width="100" height="100" style="border-radius: 50%">
  <br>
  <strong>Onivaldo Miquelino</strong>
  <br>
  <a href="https://github.com/ninomiquelino">@ninomiquelino</a>
</div>

---

# 🔒 PHP CSRF Protector: Session-Based Token Demo

![Made with PHP](https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white)
![Frontend JavaScript](https://img.shields.io/badge/Frontend-JavaScript-F7DF1E?logo=javascript&logoColor=black)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-38B2AC?logo=tailwindcss&logoColor=white)
![License MIT](https://img.shields.io/badge/License-MIT-green)
![Status Stable](https://img.shields.io/badge/Status-Stable-success)
![Version 1.0.0](https://img.shields.io/badge/Version-1.0.0-blue)
![GitHub stars](https://img.shields.io/github/stars/NinoMiquelino/csrf-token-sessao?style=social)
![GitHub forks](https://img.shields.io/github/forks/NinoMiquelino/csrf-token-sessao?style=social)
![GitHub issues](https://img.shields.io/github/issues/NinoMiquelino/csrf-token-sessao)

Este projeto ilustra a implementação de um sistema robusto de proteção contra ataques **CSRF (Cross-Site Request Forgery)**. O backend é construído em PHP, utilizando funções de segurança de sessão e geração de tokens de alta entropia. O frontend, estilizado com Tailwind CSS, permite testar o fluxo de criação, envio e validação do token.

---

## 🛡️ Principais Características de Segurança

* **Token de Alta Entropia:** O token é gerado com `random_bytes`, hash SHA-512 e dados exclusivos do usuário (IP e User-Agent) para garantir imprevisibilidade.
* **Sessão Segura (`sec_session_start`):** Implementa práticas de segurança de sessão, incluindo uso de cookies `HttpOnly`, verificação de HTTPS (`Secure`) e regeneração de ID de sessão (`session_regenerate_id(true)`).
* **Validade do Token:** Tokens são configurados para expirar após 1 hora (3600 segundos).
* **Uso Único (Single-Use Token):** Após uma verificação bem-sucedida, o token é imediatamente invalidado (`unset($_SESSION['csrf_token'])`), prevenindo ataques de *token replay*.
* **Comparação Segura:** Utiliza `hash_equals()` para comparar o token fornecido pelo usuário com o token da sessão, mitigando ataques de *timing* (tempo de execução).

---

## 🧠 Tecnologias utilizadas

* **Backend:** PHP 7.3+ (com foco em funções de segurança e manipulação de sessão).
* **Frontend:** HTML5 e JavaScript Vanilla.
* **Estilização:** Tailwind CSS (via CDN).
* **Comunicação:** Requisições assíncronas via `fetch` (POST).

---

## 🧩 Estrutura do Projeto
```
csrf-token-sessao/
├── index.html
├── api.php
├── README.md
├── .gitignore
└── LICENSE
```
---

## 🚀 Como Executar o Projeto

### Pré-requisitos

Você precisa de um ambiente de desenvolvimento web com PHP (ex: XAMPP, WAMP, MAMP, ou o servidor embutido do PHP).

### 1. Clonar o Repositório

   ```bash
   git clone https://github.com/ninomiquelino/csrf-token-sessao.git
   ```
2. Configuração do Servidor
​O sucesso deste projeto depende da funcionalidade das sessões PHP.

1. Iniciar o Servidor: A maneira mais fácil de testar é usando o servidor embutido do PHP:
```bash
# Execute na raiz do projeto
php -S localhost:8001
```
2. Acessar a Aplicação: Abra o frontend no seu navegador: http://localhost:8001/public/index.html.
​Nota de Ambiente: O código está configurado para funcionar em http://localhost. Em ambientes de produção (domínios reais), você DEVE usar HTTPS, pois o sec_session_start habilita o flag Secure nos cookies quando detecta HTTPS.

3. Testando o Fluxo de Segurança
​Siga a sequência abaixo para testar a funcionalidade completa de proteção CSRF:
​Teste 1: Fluxo de Sucesso (Criação e Verificação)
​Clique em 1. Criar Token CSRF.
​Resultado: O PHP inicia a sessão, gera um novo token e o armazena. O token é exibido no frontend.
​Clique em 2. Verificar Token CSRF.
​Resultado: O frontend envia o token. O PHP o verifica com sucesso, invalida o token da sessão, e o status mostra sucesso.
​Tente clicar em 2. Verificar Token CSRF novamente.
​Resultado: O teste falha. O status mostrará erro (Falha na verificação...), pois o token foi invalidado após o primeiro uso, prevenindo ataques de repetição.
​Teste 2: Falha por Token Ausente
​Crie um novo token.
​Recarregue a página: Isso simula um ataque de CSRF onde a requisição maliciosa não possui o token correto. O token antigo ainda está na sessão do PHP, mas o frontend perdeu a referência.
​Clique em 2. Verificar Token CSRF.
​Resultado: O status falha, pois o token enviado é vazio.
​<br>
🛑 Considerações Finais e Segurança
​Local de Armazenamento: Em aplicações reais, o token é geralmente inserido em um campo oculto (<input type="hidden">) dentro do formulário HTML, ou enviado via cabeçalho HTTP personalizado (X-CSRF-Token). Este demo utiliza ambos os métodos no JavaScript para cobrir os casos de uso mais comuns.
​Complexidade do Token: As funções de geração de token fornecidas são altamente complexas. Para muitos frameworks, gerar um token aleatório simples de 32 bytes (random_bytes(32)) e armazená-lo já é suficiente, mas a inclusão de dados do ambiente (como IP e User-Agent) adiciona uma camada de segurança de impressão digital.
​Desenvolvimento vs. Produção: Lembre-se de revisar os cabeçalhos CORS (Access-Control-Allow-Origin: *) em src/api.php antes de implantar em produção, substituindo * pelo domínio real do seu frontend.

---

## 🤝 Contribuições
Contribuições são sempre bem-vindas!  
Sinta-se à vontade para abrir uma [*issue*](https://github.com/NinoMiquelino/csrf-token-sessao/issues) com sugestões ou enviar um [*pull request*](https://github.com/NinoMiquelino/csrf-token-sessao/pulls) com melhorias.

---

## 💬 Contato
📧 [Entre em contato pelo LinkedIn](https://www.linkedin.com/in/onivaldomiquelino/)  
💻 Desenvolvido por **Onivaldo Miquelino**

---
