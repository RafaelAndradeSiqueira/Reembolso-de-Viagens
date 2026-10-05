import api from './api'

export default {
  async gerar({ ano, mes, datasExcluidas }) {
    const { data } = await api.post('/reembolsos/gerar', {
      ano,
      mes,
      datas_excluidas: datasExcluidas,
    })
    return data
  },
}
