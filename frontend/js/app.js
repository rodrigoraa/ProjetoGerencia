const apiUrl = "http://localhost/projeto-gerencia/backend/api.php";

const form = document.getElementById("formPrato");
const pratoIdInput = document.getElementById("pratoId");
const nomeInput = document.getElementById("nome");
const descricaoInput = document.getElementById("descricao");
const precoInput = document.getElementById("preco");
const categoriaInput = document.getElementById("categoria");
const tabelaPratos = document.getElementById("tabelaPratos");
const btnCancelar = document.getElementById("btnCancelar");

async function listarPratos() {
  try {
    const response = await fetch(`${apiUrl}/pratos`);
    if (!response.ok) {
      throw new Error("Erro ao buscar pratos.");
    }
    const pratos = await response.json();

    tabelaPratos.innerHTML = "";
    pratos.forEach((prato) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
                <td>${prato.nome}</td>
                <td>R$ ${parseFloat(prato.preco).toFixed(2)}</td>
                <td>${prato.categoria}</td>
                <td>
                    <button class="btn-editar">Editar</button>
                    <button class="btn-excluir">Excluir</button>
                </td>
            `;
      tr.querySelector(".btn-editar").addEventListener("click", () =>
        prepararEdicao(prato)
      );
      tr.querySelector(".btn-excluir").addEventListener("click", () =>
        excluirPrato(prato.id)
      );

      tabelaPratos.appendChild(tr);
    });
  } catch (error) {
    console.error("Falha na requisição:", error);
  }
}

function prepararEdicao(prato) {
  pratoIdInput.value = prato.id;
  nomeInput.value = prato.nome;
  descricaoInput.value = prato.descricao;
  precoInput.value = prato.preco;
  categoriaInput.value = prato.categoria;
  window.scrollTo(0, 0);
}

function limparFormulario() {
  form.reset();
  pratoIdInput.value = "";
}

async function salvarPrato(event) {
  event.preventDefault();

  const id = pratoIdInput.value;
  const pratoData = {
    nome: nomeInput.value,
    descricao: descricaoInput.value,
    preco: precoInput.value,
    categoria: categoriaInput.value,
  };

  const method = id ? "PUT" : "POST";
  const endpoint = id ? `${apiUrl}/pratos/${id}` : `${apiUrl}/pratos`;

  try {
    const response = await fetch(endpoint, {
      method: method,
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(pratoData),
    });

    if (!response.ok) {
      throw new Error("Erro ao salvar prato.");
    }

    limparFormulario();
    listarPratos();
  } catch (error) {
    console.error("Falha ao salvar:", error);
  }
}

async function excluirPrato(id) {
  if (confirm("Tem certeza que deseja excluir este prato?")) {
    try {
      const response = await fetch(`${apiUrl}/pratos/${id}`, {
        method: "DELETE",
      });
      if (!response.ok) {
        throw new Error("Erro ao excluir prato.");
      }
      listarPratos();
    } catch (error) {
      console.error("Falha ao excluir:", error);
    }
  }
}

form.addEventListener("submit", salvarPrato);
btnCancelar.addEventListener("click", limparFormulario);
document.addEventListener("DOMContentLoaded", listarPratos);
