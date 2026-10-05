const PREFIXO = 'reembolso:'

export default {
  ler(chave, padrao = null) {
    try {
      const valor = localStorage.getItem(PREFIXO + chave)
      return valor === null ? padrao : JSON.parse(valor)
    } catch (e) {
      return padrao
    }
  },

  gravar(chave, valor) {
    try {
      localStorage.setItem(PREFIXO + chave, JSON.stringify(valor))
    } catch (e) {
    }
  },

  remover(chave) {
    try {
      localStorage.removeItem(PREFIXO + chave)
    } catch (e) {
    }
  },
}
