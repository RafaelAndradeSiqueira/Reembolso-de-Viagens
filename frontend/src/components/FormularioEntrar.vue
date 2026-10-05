<template>
  <b-card>
    <b-form @submit.prevent="enviar">
      <b-alert :show="!!erro" variant="danger" class="py-2 quebra-linha">{{ erro }}</b-alert>

      <b-form-group label="E-mail" label-for="entrar-email">
        <b-form-input id="entrar-email" v-model.trim="email" type="email" autocomplete="username" required autofocus />
      </b-form-group>

      <b-form-group label="Senha" label-for="entrar-senha">
        <b-form-input id="entrar-senha" v-model="senha" type="password" autocomplete="current-password" required />
      </b-form-group>

      <b-button type="submit" class="btn-marca" block :disabled="carregando">
        <b-spinner v-if="carregando" small class="mr-1" />
        Entrar
      </b-button>
    </b-form>

    <p class="text-center small mt-3 mb-0">
      Ainda não tem conta?
      <b-link class="text-marca font-weight-bold" @click="$emit('criar-conta')">Criar conta</b-link>
    </p>
  </b-card>
</template>

<script>
import autenticacaoService from '../services/autenticacaoService'
import { mensagemDeErro } from '../services/api'

export default {
  name: 'FormularioEntrar',

  data() {
    return { email: '', senha: '', carregando: false, erro: null }
  },

  methods: {
    async enviar() {
      this.carregando = true
      this.erro = null
      try {
        const usuario = await autenticacaoService.entrar(this.email, this.senha)
        this.$emit('entrou', usuario)
      } catch (erro) {
        this.erro = mensagemDeErro(erro)
      } finally {
        this.carregando = false
      }
    },
  },
}
</script>
