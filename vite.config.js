import { defineConfig } from 'vite'
import mjml from 'vite-plugin-mjml'

export default defineConfig({
  plugins: [
    mjml({
      input: 'resources/mail',
      output: 'resources/views/mail'
    }),
  ],
})