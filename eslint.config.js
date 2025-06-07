import prettier from 'eslint-config-prettier';
import vue from 'eslint-plugin-vue';
import { defineConfigWithVueTs, vueTsConfigs } from '@vue/eslint-config-typescript';

export default defineConfigWithVueTs(
  vue.configs['flat/strongly-recommended'], // More strict than 'essential'
  vueTsConfigs.strict,                     // Strict TypeScript rules
  {
    ignores: [
      'vendor',
      'node_modules',
      'public',
      'bootstrap/ssr',
      'tailwind.config.js',
      'resources/js/components/ui/*',
    ],
  },
  {
    rules: {
      'vue/multi-word-component-names': 'off', // Allow single-word component names for pages/layouts
      'vue/no-v-html': 'error',                  // Disallow v-html (XSS risk)
      'vue/require-prop-types': 'error',         // Props should have types
              'vue/require-default-prop': 'off',         // Allow optional props without defaults

      '@typescript-eslint/no-explicit-any': 'error',  // Disallow `any`
              '@typescript-eslint/explicit-function-return-type': 'off', // Allow inferred return types
      '@typescript-eslint/no-unused-vars': ['error', { argsIgnorePattern: '^_' }],
      '@typescript-eslint/no-inferrable-types': 'off', // Allow explicit primitive types
    },
  },
  prettier, // Must come last
);
