import { defineConfigWithVueTs, vueTsConfigs } from '@vue/eslint-config-typescript';
import prettier from 'eslint-config-prettier';
import importPlugin from 'eslint-plugin-import';
import vue from 'eslint-plugin-vue';

export default defineConfigWithVueTs(
    vue.configs['flat/essential'],
    vueTsConfigs.recommended,
    {
        ignores: [
            'vendor',
            'node_modules',
            'public',
            'bootstrap/ssr',
            // Maizzle builds the transactional emails and is CommonJS, on its
            // own toolchain. It is not part of the SPA and does not share its
            // module rules.
            'maizzle/**',
            'resources/js/components/ui/*',
            // Wayfinder regenerates these on every build with import order
            // matching PHP file scan, not alphabetical. Excluding them avoids
            // a perpetual fight between the generator and import/order.
            'resources/js/actions/**',
            'resources/js/routes/**',
        ],
    },
    {
        plugins: {
            import: importPlugin,
        },
        settings: {
            'import/resolver': {
                typescript: {
                    alwaysTryTypes: true,
                    project: './tsconfig.json',
                },
            },
        },
        rules: {
            'vue/multi-word-component-names': 'off',
            '@typescript-eslint/no-explicit-any': 'off',
            // Forms are Inertia useForm objects handed down as a prop, and the
            // child binds v-model straight onto them. The object is reactive
            // and shared by reference, so this works — but it is the pattern
            // the rule exists to catch. Turning it off here is a deliberate
            // stay of execution, not an endorsement: moving LinkForm.vue onto
            // defineModel is a refactor of the main form and belongs in its
            // own change, not in a CI/CD one.
            'vue/no-mutating-props': 'off',
            'import/order': [
                'error',
                {
                    groups: ['builtin', 'external', 'internal', 'parent', 'sibling', 'index'],
                    'newlines-between': 'always',
                    alphabetize: {
                        order: 'asc',
                        caseInsensitive: true,
                    },
                },
            ],
        },
    },
    prettier,
);
