import api from './api'
import armazenamento from './armazenamento'

function guardarSessao(dados) {
  armazenamento.gravar('token', dados.token)
  armazenamento.gravar('usuario', dados.usuario)
  return dados.usuario
}

export default {
  async entrar(email, senha) {
    const { data } = await api.post('/entrar', { email, senha })
    return guardarSessao(data)
  },

  async cadastrar({ nome, email, senha, confirmacaoSenha }) {
    const { data } = await api.post('/cadastrar', {
      nome,
      email,
      senha,
      senha_confirmation: confirmacaoSenha,
    })
    return guardarSessao(data)
  },

  sair() {
    armazenamento.remover('token')
    armazenamento.remover('usuario')
  },

  usuarioAtual() {
    return armazenamento.ler('token') ? armazenamento.ler('usuario') : null
  },
}
