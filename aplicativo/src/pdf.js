// PDF das fichas: a API exige o token do aparelho, então baixa como blob. No celular abre o "Compartilhar"
// (WhatsApp, salvar em arquivos...) — no app instalado (iPhone) o download direto deixa a pessoa presa no PDF.
import { api } from 'boot/api'

export async function baixarPdf (url) {
  const resposta = await api.get(url, { responseType: 'blob' })
  const disposicao = resposta.headers['content-disposition'] || ''
  const nome = (disposicao.match(/filename="([^"]+)"/) || [])[1] || 'Ficha.pdf'

  const celular = window.matchMedia && window.matchMedia('(pointer: coarse)').matches
  const arquivo = new File([resposta.data], nome, { type: 'application/pdf' })
  if (celular && navigator.canShare && navigator.canShare({ files: [arquivo] })) {
    try {
      await navigator.share({ files: [arquivo], title: nome })
      return
    } catch (erro) {
      if (erro.name === 'AbortError') return // a pessoa fechou o compartilhar
      // NotAllowedError (o navegador recusou depois da espera do download): cai no download comum
    }
  }

  const link = URL.createObjectURL(resposta.data)
  const a = document.createElement('a')
  a.href = link
  a.download = nome
  document.body.appendChild(a)
  a.click()
  a.remove()
  setTimeout(() => URL.revokeObjectURL(link), 60000)
}
