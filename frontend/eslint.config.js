import globals from 'globals'
import pluginVue from 'eslint-plugin-vue'

export default [
  {
    files: ['**/*.{js,vue}'],
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.node,
      },
    },
  },
  ...pluginVue.configs['flat/recommended'],
]
