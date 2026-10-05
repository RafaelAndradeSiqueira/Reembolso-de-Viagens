<template>
  <b-form @submit.prevent="enviar">
    <b-alert :show="!!erro" variant="danger" class="py-2 quebra-linha">{{ erro }}</b-alert>

    <b-form-group
      v-for="(nome, dia) in diasSemana"
      :key="dia"
      :label="nome"
      :label-for="`valor-${dia}`"
      label-cols="5"
      label-cols-sm="4"
    >
      <b-input-group prepend="R$">
        <b-form-input
          :id="`valor-${dia}`"
          v-model="formulario[dia]"
          type="number"
          min="0"
          step="0.01"
          inputmode="decimal"
          placeholder="0,00"
          required
        />
      </b-input-group>
    </b-form-group>

    <b-button type="submit" class="btn-marca" block :disabled="carregando">
      <b-spinner v-if="carregando" small class="mr-1" />
      {{ textoBotao }}
    </b-button>
  </b-form>
</template>

<script>
import valoresService, { DIAS_SEMANA } from '../services/valoresService'
import { mensagemDeErro } from '../services/api'

function montarFormulario(valores) {
  const formulario = {}
  Object.keys(DIAS_SEMANA).forEach((dia) => {
    formulario[dia] = valores && valores[dia] !== undefined ? valores[dia] : ''
  })
  return formulario
}

export default {
  name: 'FormularioValores',

  props: {
    valores: { type: Object, default: null },
    textoBotao: { type: String, default: 'Salvar' },
  },

  data() {
    return {
      diasSemana: DIAS_SEMANA,
      formulario: montarFormulario(this.valores),
      carregando: false,
      erro: null,
    }
  },

  watch: {
    valores(novosValores) {
      this.formulario = montarFormulario(novosValores)
    },
  },

  methods: {
    async enviar() {
      this.carregando = true
      this.erro = null
      try {
        const valores = {}
        Object.keys(this.formulario).forEach((dia) => {
          valores[dia] = Number(this.formulario[dia])
        })
        this.$emit('salvou', await valoresService.salvar(valores))
      } catch (erro) {
        this.erro = mensagemDeErro(erro)
      } finally {
        this.carregando = false
      }
    },
  },
}
</script>
