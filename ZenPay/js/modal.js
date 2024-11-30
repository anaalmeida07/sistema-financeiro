  // Selecionar elementos do DOM
  const btnReceita = document.getElementById('btnReceita');
  const modalReceita = document.getElementById('addReceitaModal');
  const closeReceitaModal = document.getElementById('closeReceitaModal');

  // Abrir o modal
  btnReceita.addEventListener('click', (event) => {
      event.preventDefault(); // Impede comportamento padrão do link
      modalReceita.style.display = 'block'; // Mostra o modal
  });

  // Fechar o modal ao clicar no botão de fechar
  closeReceitaModal.addEventListener('click', () => {
      modalReceita.style.display = 'none';
  });

  // Fechar o modal ao clicar fora do conteúdo
  window.addEventListener('click', (event) => {
      if (event.target === modalReceita) {
          modalReceita.style.display = 'none';
      }
  });

  // Seleciona os elementos
const btnDespesa = document.getElementById('btnDespesa');
const despesaModal = document.getElementById('addDespesaModal');
const closeDespesaModal = document.getElementById('closeDespesaModal');

// Abre o modal ao clicar no botão
btnDespesa.addEventListener('click', function (e) {
    e.preventDefault(); // Evita comportamento padrão do link
    despesaModal.style.display = 'block';
});

// Fecha o modal ao clicar no botão de fechar
closeDespesaModal.addEventListener('click', function () {
    despesaModal.style.display = 'none';
});

// Fecha o modal ao clicar fora da área de conteúdo
window.addEventListener('click', function (e) {
    if (e.target === despesaModal) {
        despesaModal.style.display = 'none';
    }
});


    // Abrir e fechar o modal
    const btnContaBancaria = document.getElementById('btnContaBancaria');
    const modal = document.getElementById('addContaModal');
    const closeModal = document.getElementById('closeContaModal');

    btnContaBancaria.addEventListener('click', (event) => {
        event.preventDefault(); // Evita comportamento padrão do link
        modal.style.display = 'block'; // Mostra o modal
    });

    closeModal.addEventListener('click', () => {
        modal.style.display = 'none'; // Esconde o modal
    });

    // Fecha o modal ao clicar fora do conteúdo
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });



      // JavaScript para abrir e fechar o modal do extrato
      const btnExtrato = document.getElementById('btnExtrato');
      const extratoModal = document.getElementById('extratoModal');
      const closeExtratoModal = document.getElementById('closeExtratoModal');
  
      btnExtrato.addEventListener('click', (event) => {
          event.preventDefault();
          extratoModal.style.display = 'block'; // Mostra o modal
      });
  
      closeExtratoModal.addEventListener('click', () => {
          extratoModal.style.display = 'none'; // Fecha o modal
      });
  
      // Fecha o modal ao clicar fora do conteúdo
      window.addEventListener('click', (event) => {
          if (event.target === extratoModal) {
              extratoModal.style.display = 'none';
          }
      });