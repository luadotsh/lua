import {
    defineConfigWithVueTs,
    vueTsConfigs,
} from '@vue/eslint-config-typescript';
import prettier from 'eslint-config-prettier';
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
            'resources/js/wayfinder/**',
        ],
    },
    {
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
        },
    },
    // SSR runs these modules in Node, where there is no window and where a
    // module is evaluated once and shared by every request. Both rules below
    // come from one production failure in useCurrentUrl, and both are matched
    // against the AST rather than the source text.
    {
        files: ['resources/js/composables/**/*.ts', 'resources/js/lib/**/*.ts'],
        rules: {
            'no-restricted-syntax': [
                'error',
                {
                    // `window?.x` reads as a guard and is not one: optional
                    // chaining protects a declared binding holding null, while
                    // an undeclared window throws ReferenceError before the
                    // `?.` is reached.
                    selector:
                        'MemberExpression[optional=true][object.name=/^(window|document|localStorage)$/]',
                    message:
                        "Optional chaining does not guard a browser global — an undeclared `window` throws before `?.` runs. Use `typeof window !== 'undefined'`.",
                },
                {
                    // A page captured at module scope belongs to whichever
                    // request loaded the module first, and anything computed
                    // from it caches that request's URL for all the others.
                    selector:
                        "Program > VariableDeclaration > VariableDeclarator[init.callee.name='usePage']",
                    message:
                        'Call usePage() inside the composable. At module scope it is shared across every SSR request.',
                },
            ],
        },
    },
    prettier,
);
