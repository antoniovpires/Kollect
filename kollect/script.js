const acc = document.getElementById("itens-accordion");
const editModal = new bootstrap.Modal('#modal-edit');

const loadModal = (info) => {
  document.querySelector(".editBtn").style.display = info ? "" : "none";
  document.querySelector(".deleteBtn").style.display = info ? "" : "none";
  document.querySelector(".createBtn").style.display = info ? "none" : "";
  document.querySelector("#exampleModalLabel").textContent = info ? "Editing product" : "Create new product";


  document.querySelectorAll(".meId").forEach((element) => {
    element.value = info ? info.id : "";
  })
  document.getElementById("meName").value = info ? info.name : "";
  document.getElementById("meDesc").value = info ? info.description : "";
  document.getElementById("meYear").value = info ? info.year : "";
  document.getElementById("flexCheckChecked").checked = info ? info.is_available : "";
  editModal.show();
}

const editProduct = (id) => {
  axios.get(`http://127.0.0.1/api_rest/products/${id}`)
  .then(function (response) {
    loadModal (response.data)
  })
  .catch(function (error) {
    alert(error);
  });
}

document.querySelectorAll('.actionform').forEach((form) => {
  form.addEventListener('submit', function (event) {
    event.preventDefault(); // Impede o envio padrão do formulário
  
    // Coleta os dados do formulário
    const formData = new FormData(event.target);
  
    // Configuração da solicitação Axios
    const config = {
        method: 'post', // Método HTTP usado para enviar o formulário
        url: 'http://127.0.0.1/api_rest/products/', // URL para processamento do formulário
        data: formData,
        responseType: 'json', // Espera uma resposta JSON
    };
  
    // Envia a solicitação Axios
    axios(config)
    .then(function (response) {
        // Processar a resposta JSON aqui e atualizar a página conforme necessário
        Swal.fire(
          'Success',
          response.data.message,
          'success'
        )
        loadData();
    })
    .catch(function (error) {
        // Lidar com erros, se houver
        console.error(error);
    });
  });
})

const loadData = () => {
  axios.get("http://127.0.0.1/api_rest/products")
  .then(function (response) {
    let itens = response.data;
    acc.innerHTML = "";

    for (let u = 0; u < itens.length; u++) {
        acc.insertAdjacentHTML("beforeend", `
        <div class="accordion-item">
            <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${u}" aria-expanded="true" aria-controls="collapse${u}">
                ${itens[u].name} - ${itens[u].year}
            </button>
            </h2>
            <div id="collapse${u}" class="accordion-collapse collapse" data-bs-parent="#itens-accordion">
            <div class="accordion-body">
                ${itens[u].description}
                <div class="accordion-buttons">
                    <button id="edit${itens[u].id}" type="button" class="btn btn-primary">
                      Edit
                    </button>
                </div>
            </div>
            </div>
        </div>
        `)
  
        document.getElementById(`edit${itens[u].id}`).addEventListener("click", () => {
          editProduct(itens[u].id)
        })
    }
  
    document.getElementById(`addItem`).addEventListener("click", () => {
      loadModal()
    })
  
  })
  .catch(function (error) {
    // handle error
    console.log(error);
  })
  .finally(function () {
    // always executed
  });
}

window.addEventListener("load", loadData())