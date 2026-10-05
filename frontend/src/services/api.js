import axios from 'axios'
import armazenamento from './armazenamento'

const api = axios.create({
  baseURL: (import.meta.env.VITE_API_URL || 'http://localhost:8000') + '/api',
  headers: { Accept: 'application/json' },
  timeout: 90000,
})

api.interceptors.request.use((config) => {
  const token = armazenamento.ler('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

let aoExpirarSessao = () => {}
export function definirAoExpirarSessao(funcao) {
  aoExpirarSessao = funcao
}

api.interceptors.response.use(
  (resposta) => resposta,
  (erro) => {
    if (erro.response && erro.response.status === 401) {
      aoExpirarSessao()
    }
    return Promise.reject(erro)
  }
)

export function mensagemDeErro(erro) {
  if (!erro.response) {
    return 'Não foi possível falar com o servidor. Verifique sua internet.'
  }
  if (erro.response.status === 429) {
    return 'Muitas tentativas seguidas. Espere um minutinho e tente de novo.'
  }

  const dados = erro.response.data || {}

  if (dados.errors) {
    return [].concat(...Object.values(dados.errors)).join('\n')
  }

  return dados.message || 'Algo deu errado. Tente novamente.'
}

export default api
