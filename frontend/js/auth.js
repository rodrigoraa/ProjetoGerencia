const baseUrl = "http://localhost/projeto-gerencia/backend";

const loginContainer = document.getElementById("login-container");
const registerContainer = document.getElementById("register-container");
const showRegisterLink = document.getElementById("showRegister");
const showLoginLink = document.getElementById("showLogin");

const formLogin = document.getElementById("formLogin");
const formCadastro = document.getElementById("formCadastro");

const loginMessage = document.getElementById("loginMessage");
const registerMessage = document.getElementById("registerMessage");

function exibirMensagem(element, message, type = "error") {
  element.textContent = message;
  element.className = `message ${type}`;
}

showRegisterLink.addEventListener("click", (e) => {
  e.preventDefault();
  loginContainer.style.display = "none";
  registerContainer.style.display = "block";
});

showLoginLink.addEventListener("click", (e) => {
  e.preventDefault();
  registerContainer.style.display = "none";
  loginContainer.style.display = "block";
});

formCadastro.addEventListener("submit", async (e) => {
  e.preventDefault();

  registerMessage.style.display = "none";

  const data = {
    nome: document.getElementById("regNome").value,
    email: document.getElementById("regEmail").value,
    cpf: document.getElementById("regCpf").value,
    senha_plana: document.getElementById("regSenha").value,
  };

  try {
    const response = await fetch(`${baseUrl}/cadastro`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.message || "Erro ao cadastrar.");
    }

    exibirMensagem(
      registerMessage,
      "Cadastro realizado com sucesso! Você já pode fazer o login.",
      "success"
    );
    formCadastro.reset();

    setTimeout(() => {
      showLoginLink.click();
      registerMessage.style.display = "none";
    }, 2000);
  } catch (error) {
    exibirMensagem(registerMessage, error.message, "error");
  }
});

formLogin.addEventListener("submit", async (e) => {
  e.preventDefault();

  loginMessage.style.display = "none";

  const data = {
    email: document.getElementById("loginEmail").value,
    senha_plana: document.getElementById("loginSenha").value,
  };

  try {
    const response = await fetch(`${baseUrl}/login`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.message || "Erro ao fazer login.");
    }

    exibirMensagem(
      loginMessage,
      "Login bem-sucedido! Redirecionando...",
      "success"
    );

    setTimeout(() => {
      window.location.href = "index.html";
    }, 1500);
  } catch (error) {
    exibirMensagem(loginMessage, error.message, "error");
  }
});
