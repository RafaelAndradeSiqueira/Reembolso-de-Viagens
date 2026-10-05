<template>
  <b-card>
    <div class="text-center mb-3">
      <small class="text-muted text-uppercase">Reembolso de {{ mesPorExtenso }}</small>
      <div class="display-4 font-weight-bold text-marca dinheiro">{{ formatarDinheiro(resultado.total_esperado) }}</div>
      <small class="text-muted">{{ resultado.dias_uteis }} dias úteis trabalhados</small>
    </div>

    <b-alert v-if="resultado.confere" show variant="success" class="py-2 small">
      <b-icon-check-circle-fill /> Conferido: a conta da IA bate com o cálculo do sistema.
    </b-alert>
    <b-alert v-else show variant="warning" class="py-2 small">
      <b-icon-exclamation-triangle-fill />
      A IA errou a conta: chegou em <strong>{{ formatarDinheiro(resultado.ia.total) }}</strong>.
      O valor acima é a soma correta, feita pelo sistema.
    </b-alert>

    <p v-if="resultado.ia.explicacao" class="small text-muted mb-3">
      <b-icon-chat-quote /> {{ resultado.ia.explicacao }}
    </p>

    <b-table-lite :items="linhas" :fields="colunas" small responsive class="mb-2">
      <template #cell(valor)="{ value }"><span class="dinheiro">{{ formatarDinheiro(value) }}</span></template>
      <template #cell(subtotal)="{ value }"><span class="dinheiro">{{ formatarDinheiro(value) }}</span></template>
    </b-table-lite>

    <small class="text-muted d-block text-right">
      Modelo: <code>{{ resultado.ia.modelo }}</code>
    </small>
  </b-card>
</template>

<script>
import { formatarDinheiro, MESES } from '../formatacao'

export default {
  name: 'ResultadoReembolso',

  props: {
    resultado: { type: Object, required: true },
  },

  data() {
    return {
      colunas: [
        { key: 'nome_dia', label: 'Dia' },
        { key: 'quantidade', label: 'Qtd.', class: 'text-center' },
        { key: 'valor', label: 'Valor', class: 'text-right' },
        { key: 'subtotal', label: 'Subtotal', class: 'text-right' },
      ],
    }
  },

  computed: {
    mesPorExtenso() {
      return `${MESES[this.resultado.mes - 1].toLowerCase()} de ${this.resultado.ano}`
    },

    linhas() {
      return this.resultado.detalhamento.filter((linha) => linha.quantidade > 0)
    },
  },

  methods: {
    formatarDinheiro,
  },
}
</script>
