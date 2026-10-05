<template>
  <b-card>
    <h5 class="mb-3">Criar conta</h5>

    <b-form @submit.prevent="enviar">
      <b-alert :show="!!erro" variant="danger" class="py-2 quebra-linha">{{ erro }}</b-alert>

      <b-form-group label="Nome" label-for="cadastro-nome">
        <b-form-input id="cadastro-nome" v-model.trim="formulario.nome" autocomplete="name" maxlength="100" required autofocus />
      </b-form-group>

      <b-form-group label="E-mail" label-for="cadastro-email">
        <b-form-input id="cadastro-email" v-model.trim="formulario.email" type="email" autocomplete="email" required />
      </b-form-group>

      <b-form-group label="Senha" label-for="cadastro-senha" description="Mínimo de 8 caracteres.">
        <b-form-input
          id="cadastro-senha"
          v-model="formulario.senha"
          type="password"
          autocomplete="new-password"
          minlength="8"
          required
        />
      </b-form-group>

      <b-form-group label="Confirme a senha" label-for="cadastro-confirmacao">
        <b-form-input
          id="cadastro-confirmacao"
          v-model="formulario.confirmacaoSenha"
          type="password"
          autocomplete="new-password"
          :state="senhasConferem"
          required
        />
        <b-form-invalid-feedback>As senhas não são iguais.</b-form-invalid-feedback>
      </b-form-group>

      <b-button type="submit" class="btn-marca" block :disabled="carregando || senhasConferem === false">
        <b-spinner v-if="carregando" small class="mr-1" />
        Criar conta
      </b-button>
    </b-form>

    <p class="text-center small mt-3 mb-0">
      Já tem conta?
      <b-link class="text-marca font-weight-bold" @click="$emit('voltar')">Entrar</b-link>
    </p>
  </b-card>
</template>

<script>
import autenticacaoService from '../services/autenticacaoService'
import { mensagemDeErro } from '../services/api'

export default {
  name: 'FormularioCadastro',

  data() {
    return {
      formulario: { nome: '', email: '', senha: '', confirmacaoSenha: '' },
      carregando: false,
      erro: null,
    }
  },

  computed: {
    senhasConferem() {
      if (!this.formulario.confirmacaoSenha) return null
      return this.formulario.senha === this.formulario.confirmacaoSenha
    },
  },

  methods: {
    async enviar() {
      this.carregando = true
      this.erro = null
      try {
        const usuario = await autenticacaoService.cadastrar(this.formulario)
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
