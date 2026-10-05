const moeda = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' })

export function formatarDinheiro(valor) {
  return moeda.format(Number(valor) || 0)
}

export function formatarNumeroCurto(valor) {
  return Number(valor).toLocaleString('pt-BR', { maximumFractionDigits: 2 })
}

export const MESES = [
  'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
  'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
]

export function paraDataIso(ano, mes, dia) {
  return `${ano}-${String(mes).padStart(2, '0')}-${String(dia).padStart(2, '0')}`
}

export function diaDaSemana(ano, mes, dia) {
  const diaJs = new Date(ano, mes - 1, dia).getDay()
  return diaJs === 0 ? 7 : diaJs
}

export function totalDeDiasNoMes(ano, mes) {
  return new Date(ano, mes, 0).getDate()
}
