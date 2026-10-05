import api from './api'

export const DIAS_SEMANA = {
  1: 'Segunda-feira',
  2: 'Terça-feira',
  3: 'Quarta-feira',
  4: 'Quinta-feira',
  5: 'Sexta-feira',
}

export default {
  async buscar() {
    const { data } = await api.get('/valores')
    return data.valores
  },

  async salvar(valores) {
    const { data } = await api.put('/valores', { valores })
    return data.valores
  },
}
