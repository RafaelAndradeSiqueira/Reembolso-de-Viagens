<template>
  <b-row>
    <b-col lg="7" class="mb-4">
      <b-card>
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
          <h5 class="mb-2 mb-sm-0">Escolha o mês</h5>
          <div class="d-flex">
            <b-form-select v-model="mes" :options="opcoesMes" size="sm" class="mr-2" aria-label="Mês" />
            <b-form-select v-model="ano" :options="opcoesAno" size="sm" style="width: 6rem" aria-label="Ano" />
          </div>
        </div>

        <calendario-mes :ano="ano" :mes="mes" :valores="valores" :datas-excluidas="datasExcluidas" @alternar="alternarData" />

        <p class="small text-muted mt-3 mb-3">
          <b-icon-info-circle /> Clique num dia para marcar como <strong>folga ou feriado</strong>
          (ele deixa de contar). {{ quantidadeDiasUteis }} dias úteis serão enviados.
        </p>

        <b-button class="btn-marca" size="lg" block :disabled="carregando || quantidadeDiasUteis === 0" @click="gerar">
          <template v-if="carregando">
            <b-spinner small class="mr-2" /> Calculando com a IA...
          </template>
          <template v-else><b-icon-stars class="mr-1" /> Gerar reembolso</template>
        </b-button>
      </b-card>
    </b-col>

    <b-col lg="5">
      <b-alert :show="!!erro" variant="danger" dismissible class="quebra-linha" @dismissed="erro = null">{{ erro }}</b-alert>

      <resultado-reembolso v-if="resultado" :resultado="resultado" />

      <b-card v-else class="text-center text-muted py-4">
        <div class="display-4 mb-2">🧾</div>
        <p class="mb-0">
          Confira os dias no calendário e clique em <strong>Gerar reembolso</strong>.
          A IA vai calcular quanto a empresa te deve.
        </p>
      </b-card>
    </b-col>
  </b-row>
</template>

<script>
import CalendarioMes from './CalendarioMes.vue'
import ResultadoReembolso from './ResultadoReembolso.vue'
import reembolsoService from '../services/reembolsoService'
import { mensagemDeErro } from '../services/api'
import { diaDaSemana, MESES, totalDeDiasNoMes } from '../formatacao'

export default {
  name: 'PainelReembolso',
  components: { CalendarioMes, ResultadoReembolso },

  props: {
    valores: { type: Object, required: true },
  },

  data() {
    const hoje = new Date()
    return {
      ano: hoje.getFullYear(),
      mes: hoje.getMonth() + 1,
      datasExcluidas: [],
      resultado: null,
      carregando: false,
      erro: null,
    }
  },

  computed: {
    opcoesMes() {
      return MESES.map((text, indice) => ({ value: indice + 1, text }))
    },

    opcoesAno() {
      const anoAtual = new Date().getFullYear()
      return [anoAtual - 1, anoAtual, anoAtual + 1]
    },

    quantidadeDiasUteis() {
      let quantidade = 0
      for (let dia = 1; dia <= totalDeDiasNoMes(this.ano, this.mes); dia++) {
        if (diaDaSemana(this.ano, this.mes, dia) <= 5) quantidade++
      }
      return quantidade - this.datasExcluidas.length
    },
  },

  watch: {
    ano() {
      this.limpar()
    },
    mes() {
      this.limpar()
    },
    valores() {
      this.resultado = null
    },
  },

  methods: {
    limpar() {
      this.datasExcluidas = []
      this.resultado = null
      this.erro = null
    },

    alternarData(data) {
      const posicao = this.datasExcluidas.indexOf(data)
      if (posicao === -1) {
        this.datasExcluidas.push(data)
      } else {
        this.datasExcluidas.splice(posicao, 1)
      }
      this.resultado = null
    },

    async gerar() {
      this.carregando = true
      this.erro = null
      try {
        this.resultado = await reembolsoService.gerar({
          ano: this.ano,
          mes: this.mes,
          datasExcluidas: this.datasExcluidas,
        })
      } catch (erro) {
        this.erro = mensagemDeErro(erro)
      } finally {
        this.carregando = false
      }
    },
  },
}
</script>
