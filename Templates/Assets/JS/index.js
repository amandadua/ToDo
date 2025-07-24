const texts = [
  "Gerenciador de Projetos",
  "Lista de Tarefas",
  "Relatórios Automáticos",
];
let idx = 0;
const el = document.getElementById("rotating-text");

setInterval(() => {
  el.style.opacity = 0;
  setTimeout(() => {
    idx = (idx + 1) % texts.length;
    el.textContent = texts[idx];
    el.style.opacity = 1;
  }, 300);
}, 2000);