const colors = {

    moss: {

        25: '#FAFDF7',

        50: '#F5FBEE',

        100: '#E6F4D7',

        200: '#CEEAB0',

        300: '#ACDC79',

        400: '#86CB3C',

        500: '#669F2A',

        600: '#4F7A21',

        700: '#3F621A',

        800: '#335015',

        900: '#2B4212',

    },

    'green-light': {

        25: '#FAFEF5',

        50: '#F3FEE7',

        100: '#E4FBCC',

        200: '#D0F8AB',

        300: '#A6EF67',

        400: '#85E13A',

        500: '#85E13A',

        600: '#4CA30D',

        700: '#3B7C0F',

        800: '#326212',

        900: '#2B5314',

    },

    green: {

        'fks': '#CBFF00',

        default: '#61F34B',

        25: '#F6FEF9',

        50: '#EDFCF2',

        100: '#D3F8DF',

        200: '#AAF0C4',

        300: '#73E2A3',

        400: '#3CCB7F',

        500: '#16B364',

        600: '#099250',

        700: '#087443',

        800: '#095C37',

        900: '#084C2E',

    },

    teal: {

        25: '#F6FEFC',

        50: '#F0FDF9',

        100: '#CCFBEF',

        200: '#99F6E0',

        300: '#5FE9D0',

        400: '#2ED3B7',

        500: '#15B79E',

        600: '#0E9384',

        700: '#107569',

        800: '#125D56',

        900: '#134E48',

    },

    cyan: {

        25: '#F5FEFF',

        50: '#ECFDFF',

        100: '#CFF9FE',

        200: '#A5F0FC',

        300: '#67E3F9',

        400: '#22CCEE',

        500: '#06AED4',

        600: '#088AB2',

        700: '#0E7090',

        800: '#155B75',

        900: '#164C63',

    },

    'blue-light': {

        25: '#F5FBFF',

        50: '#F0F9FF',

        100: '#E0F2FE',

        200: '#B9E6FE',

        300: '#7CD4FD',

        400: '#36BFFA',

        500: '#0BA5EC',

        600: '#0086C9',

        700: '#026AA2',

        800: '#065986',

        900: '#0B4A6F',

    },

    blue: {

        25: '#F5FAFF',

        50: '#EFF8FF',

        100: '#D1E9FF',

        200: '#B2DDFF',

        300: '#84CAFF',

        400: '#53B1FD',

        500: '#2E90FA',

        600: '#1570EF',

        700: '#175CD3',

        800: '#1849A9',

        900: '#194185',

        'fks': '#2F49D9'

    },

    'blue-dark': {

        25: '#F5F8FF',

        50: '#EFF4FF',

        100: '#D1E0FF',

        200: '#B2CCFF',

        300: '#84ADFF',

        400: '#528BFF',

        500: '#2970FF',

        600: '#155EEF',

        700: '#004EEB',

        800: '#0040C1',

        900: '#00359E',

    },

    indigo: {

        25: '#F5F8FF',

        50: '#EEF4FF',

        100: '#E0EAFF',

        200: '#C7D7FE',

        300: '#A4BCFD',

        400: '#8098F9',

        500: '#6172F3',

        600: '#444CE7',

        700: '#3538CD',

        800: '#2D31A6',

        900: '#2D3282',

    },

    violet: {

        25: '#FBFAFF',

        50: '#F5F3FF',

        100: '#ECE9FE',

        200: '#DDD6FE',

        300: '#C3B5FD',

        400: '#A48AFB',

        500: '#875BF7',

        600: '#7839EE',

        700: '#6927DA',

        800: '#5720B7',

        900: '#491C96',

    },

    purple: {

        25: '#FAFAFF',

        50: '#F4F3FF',

        100: '#EBE9FE',

        200: '#D9D6FE',

        300: '#BDB4FE',

        400: '#9B8AFB',

        500: '#7A5AF8',

        600: '#6938EF',

        700: '#5925DC',

        800: '#4A1FB8',

        900: '#3E1C96',

    },

    fuchsia: {

        25: '#FEFAFF',

        50: '#FDF4FF',

        100: '#FBE8FF',

        200: '#F6D0FE',

        300: '#EEAAFD',

        400: '#E478FA',

        500: '#D444F1',

        600: '#BA24D5',

        700: '#9F1AB1',

        800: '#821890',

        900: '#6F1877',

    },

    pink: {

        25: '#FEF6FB',

        50: '#FDF2FA',

        100: '#FCE7F6',

        200: '#FCCEEE',

        300: '#FAA7E0',

        400: '#F670C7',

        500: '#EE46BC',

        600: '#DD2590',

        700: '#C11574',

        800: '#9E165F',

        900: '#851651',

    },

    rose: {

        25: '#FFF5F6',

        50: '#FFF1F3',

        100: '#FFE4E8',

        200: '#FECDD6',

        300: '#FEA3B4',

        400: '#FD6F8E',

        500: '#F63D68',

        600: '#E31B54',

        700: '#C01048',

        800: '#A11043',

        900: '#89123E',

    },

    'orange-dark': {

        25: '#FFF9F5  ',

        50: '#FFF4ED',

        100: '#FFE6D5',

        200: '#FFD6AE',

        300: '#FF9C66',

        400: '#FF692E',

        500: '#FF4405',

        600: '#E62E05',

        700: '#BC1B06',

        800: '#97180C',

        900: '#771A0D',

    },

    orange: {

        25: '#FEFAF5',

        50: '#FEF6EE',

        100: '#FDEAD7',

        200: '#F9DBAF',

        300: '#F7B27A',

        400: '#F38744',

        500: '#EF6820',

        600: '#E04F16',

        700: '#B93815',

        800: '#932F19',

        900: '#772917',

    },

    yellow: {

        25: '#FEFDF0',

        50: '#FEFBE8',

        100: '#FEF7C3',

        200: '#FEEE95',

        300: '#FDE272',

        400: '#FAC515',

        500: '#EAAA08',

        600: '#CA8504',

        700: '#A15C07',

        800: '#854A0E',

        900: '#713B12',

    },

    'gray-blue': {

        25: '#FCFCFD',

        50: '#F8F9FC',

        100: '#EAECF5',

        200: '#D5D9EB',

        300: '#B3B8DB',

        400: '#717BBC',

        500: '#4E5BA6',

        600: '#3E4784',

        700: '#363F72',

        800: '#293056',

        900: '#101323',

    },

    'gray-cool': {

        20: '#DDE1E6',

        25: '#FCFCFD',

        50: '#F9F9FB',

        100: '#EFF1F5',

        200: '#DCDFEA',

        300: '#B9C0D4',

        400: '#7D89B0',

        500: '#5D6B98',

        600: '#4A5578',

        700: '#404968',

        800: '#30374F',

        900: '#111322',

    },

    'gray-modern': {

        25: '#FCFCFD',

        50: '#F8FAFC',

        100: '#EEF2F6',

        200: '#E3E8EF',

        300: '#CDD5DF',

        400: '#9AA4B2',

        500: '#697586',

        600: '#4B5565',

        700: '#364152',

        800: '#202939',

        900: '#121926',

    },

    'gray-neutral': {

        25: '#FCFCFD',

        50: '#F9FAFB',

        100: '#F3F4F6',

        200: '#E5E7EB',

        300: '#D2D6DB',

        400: '#9DA4AE',

        500: '#6C737F',

        600: '#4D5761',

        700: '#384250',

        800: '#1F2A37',

        900: '#111927',

    },

    'gray-iron': {

        25: '#FCFCFC',

        50: '#FAFAFA',

        100: '#F4F4F5',

        200: '#E4E4E7',

        300: '#D1D1D6',

        400: '#A0A0AB',

        500: '#70707B',

        600: '#51525C',

        700: '#3F3F46',

        800: '#26272B',

        900: '#18181B',

    },

    'gray-true': {

        25: '#FCFCFC',

        50: '#FAFAFA',

        100: '#F5F5F5',

        200: '#E5E5E5',

        300: '#D6D6D6',

        400: '#A3A3A3',

        500: '#737373',

        600: '#525252',

        700: '#424242',

        800: '#292929',

        900: '#141414',

    },

    'gray-warm': {

        25: '#FDFDFC',

        50: '#FAFAF9',

        100: '#F5F5F4',

        200: '#E7E5E4',

        300: '#D7D3D0',

        400: '#A9A29D',

        500: '#79716B',

        600: '#57534E',

        700: '#44403C',

        800: '#292524',

        900: '#1C1917',

    },

    gray: {

        25: '#FCFCFD',

        50: '#F8FAFC',

        100: '#EEF2F6',

        200: '#E3E8EF',

        300: '#D6D6D6',

        400: '#9AA4B2',

        500: '#697586',

        600: '#4B5565',

        700: '#364152',

        800: '#1D2939',

        900: '#101828',

        light: '#F1F1F1',

    },

    error: {
        DEFAULT: '#F04438',

        25: '#FFFBFA',

        50: '#FEF3F2',

        100: '#FEE4E2',

        200: '#FECDCA',

        300: '#FDA29B',

        400: '#F97066',

        500: '#F04438',

        600: '#D92D20',

        700: '#B42318',

        800: '#912018',

        900: '#7A271A',

    },

    warning: {
        DEFAULT: '#F79009',

        25: '#FFFCF5',

        50: '#FFFAEB',

        100: '#FEF0C7',

        200: '#FEDF89',

        300: '#FEC84B',

        400: '#FDB022',

        500: '#F79009',

        600: '#DC6803',

        700: '#B54708',

        800: '#93370D',

        900: '#7A2E0E',

    },

    success: {
        DEFAULT: '#12B76A',

        25: '#F6FEF9',

        50: '#ECFDF3',

        100: '#D1FADF',

        200: '#A6F4C5',

        300: '#6CE9A6',

        400: '#32D583',

        500: '#12B76A',

        600: '#039855',

        700: '#027A48',

        800: '#05603A',

        900: '#054F31',

    },

    info: {
        DEFAULT: '#0075E7',
        25: '#F5FAFF',
        50: '#EFF8FF',
        100: '#D1E9FF',
        200: '#B2DDFF',
        300: '#84CAFF',
        400: '#53B1FD',
        500: '#0075E7',
        600: '#005EA4',
        700: '#004D86',
        800: '#003967',
        900: '#00284B',
    },



}



module.exports = {

    darkMode: 'class',

    content: [

        './resources/Frontend/js/**/*.blade.php',

        './resources/Frontend/js/**/*.vue',

        './node_modules/@toannguyen112/bed-library-essentials/src/components/**/*.vue',

    ],

    theme: {

        screens: {

            xs: '425px',

            sm: '640px',

            md: '768px',

            lg: '1024px',

            xl: '1440px',

            'max-xl': { max: '1439px' },

            'max-lg': { max: '1023px' },

            'max-md': { max: '767px' },

            'max-sm': { max: '639px' },

            'max-xs': { max: '424px' },

        },

        backgroundImage: {

            'gradient-blue': 'linear-gradient(330.4deg, #79716B 19.98%, #79716B 96.8%)',

            'gradient-blue-revert': 'linear-gradient(-330.4deg, #2B40B6 19.98%, #2067E3 96.8%)',

            'gradient-red': 'linear-gradient(270deg, #ED3237 0%, #B00C0B 100%)',

            'gradient-gray': 'linear-gradient(180deg, #FCFCFC 0%, #EAECF0 85.77%, #D0D5DD 112.33%)',

            'banner-courses': "url('/public/assets/images/banner-courses.png')",

            'banner-service-right': "url('/public/assets/images/service/service-banner-right.png')",

            'banner-service-left': "url('/public/assets/images/service/service-banner-left.png')",



            'banner-about-right': "url('/public/assets/images/about/about-banner-right.png')",

            'banner-about-left': "url('/public/assets/images/about/about-banner-left.png')",



            'funky-style': "url('/public/assets/images/funky-style-courses.png')",

            'advise': "url('/public/assets/images/products/bg-advise.png')",

            // 'hero-transparent': "url('/public/assets/images/banner-slide.png')",

        },



        container: {
            center: true,
            padding: {
                DEFAULT: '1rem',
                sm: '1rem',
                md: '2rem',
                lg: '2rem',
                xl: '2rem',
            },
            screens: {
                sm: '640px',
                md: '768px',
                lg: '1024px',
                xl: '1280px',
            },
        },

        extend: {

            spacing: {
                section: '5rem',       // 80px
                'section-lg': '7.5rem',  // 120px
                gutter: '1.5rem',      // 24px
            },

            colors: {

                ...colors,

                // Thành Khang Brand Palette (Figma Homepage Option 6 - 595:15360)
                primary: {
                    DEFAULT: '#005EA4',
                    hover: '#004D86',
                    active: '#003967',
                    info: '#0075E7',
                    50: '#F0F7FF',
                    100: '#C9E0FF',
                    200: '#81BDFF',
                    300: '#479BFF',
                    400: '#0075E7',
                    500: '#005EA4',
                    600: '#004D86',
                    700: '#003967',
                    800: '#00284B',
                    900: '#001C36',
                },
                secondary: {
                    DEFAULT: '#C9E0FF',
                    hover: '#81BDFF',
                    100: '#C9E0FF',
                    200: '#81BDFF',
                },
                surface: {
                    DEFAULT: '#FAFAFA',
                    white: '#FFFFFF',
                    50: '#F8FAFC',
                    100: '#EEF2F6',
                    'gray-light': '#F1F1F1',
                    light: '#FCFCFC',
                },
                ink: {
                    DEFAULT: '#00284B',
                    heading: '#00284B',
                    body: '#4B5565',
                    subtle: '#697586',
                    muted: '#9AA4B2',
                    light: '#FCFCFC',
                },
                // Figma form-field neutrals (Gray/300, Gray/500, Gray/700 — Liên hệ 939:2577)
                field: {
                    border: '#D6D6D6',
                    placeholder: '#808080',
                    label: '#424242',
                    required: '#FF4749',
                },
                'btn-slide': 'rgba(57, 76, 107, 0.8)',
                'brand-blue': '#005EA4',
                'brand-primary': '#005EA4',
                'brand-primary-hover': '#004D86',
                'brand-primary-active': '#003967',
                'brand-secondary': '#C9E0FF',
                'brand-secondary-hover': '#81BDFF',
                'brand-dark': '#00284B',
                'brand-info': '#0075E7',
                'brand-surface': '#FAFAFA',

                // Dark brand token + legacy dark-tech backward-compatible subkeys
                dark: {
                    DEFAULT: '#00284B',
                    800: '#00284B',
                    700: '#364152',
                    slate: '#364152',
                    'bg': '#111322',
                    'bg-alt': '#0B0F19',
                    'surface': '#171B30',
                    'surface-light': '#1E2B45',
                    'card': '#171B30',
                    'border': '#404968',
                    'border-light': '#5D6B98',
                    'text': '#FAFAFA',
                    'text-secondary': '#B9C0D4',
                    'text-muted': '#64748B',
                    'text-dim': '#475467',
                },

                // Backward-compatible legacy flat dark-tech aliases (used by unmigrated inner pages)
                'dark-bg': '#111322',
                'dark-bg-alt': '#0B0F19',
                'dark-surface': '#171B30',
                'dark-surface-light': '#1E2B45',
                'dark-card': '#171B30',
                'dark-hero': '#0B0D19',
                'dark-border': '#404968',
                'dark-border-light': '#5D6B98',

                // Direct Figma color aliases (backward-compatible)
                'figma-bg': '#FAFAFA',
                'figma-card': '#FFFFFF',
                'figma-hero': '#00284B',
                'figma-border': '#E3E8EF',
                'figma-border-light': '#D6D6D6',
                'figma-blue': '#005EA4',
                'figma-white': '#FAFAFA',
                'figma-subtext': '#4B5565',
                'figma-blue-text': '#C9E0FF',

                // Text Color Tokens (backward-compatible)
                'text-white': '#FAFAFA',
                'text-secondary': '#4B5565',
                'text-muted': '#9AA4B2',
                'text-dim': '#697586',
                'text-blue': '#C9E0FF',

                muted: '#9AA4B2',
                dim: '#697586',

                /**
                 * @deprecated LEGACY COLOR ALIASES
                 * Kept strictly for backward compatibility with unmigrated styling classes.
                 */
                'red-fks': '#D34000',
                'black-fks': '#18191E',
                'green-fks': '#CBFF00',
                'blue-fks': '#2F49D9',
                red: {
                    'fks': '#D34000'
                },
                black: {
                    'fks': '#18191E'
                },

            },

            boxShadow: {

                xs: '0 1px 2px 0 rgba(16, 24, 40, 0.05)',

                sm: '0px 1px 3px rgba(16, 24, 40, 0.1), 0px 1px 2px rgba(16, 24, 40, 0.06)',

                md: '0px 4px 8px -2px rgba(16, 24, 40, 0.1), 0px 2px 4px -2px rgba(16, 24, 40, 0.06)',

                lg: '0px 12px 16px -4px rgba(16, 24, 40, 0.08), 0px 4px 6px -2px rgba(16, 24, 40, 0.03)',

                xl: '0px 20px 24px -4px rgba(16, 24, 40, 0.08), 0px 8px 8px -4px rgba(16, 24, 40, 0.03)',

                '2xl': '0px 24px 48px -12px rgba(16, 24, 40, 0.18)',

                '3xl': '0px 32px 64px -12px rgba(16, 24, 40, 0.14)',

                'xs-error-100': '0px 1px 2px rgba(16, 24, 40, 0.05), 0px 0px 0px 4px #fee4e2',

                'xs-primary-300': '0px 1px 2px rgba(16, 24, 40, 0.05), 0px 0px 0px 4px #C9E0FF',
                'glow-primary': '0px 4px 14px rgba(0, 94, 164, 0.35)',
                'glow-primary-lg': '0px 8px 24px rgba(0, 94, 164, 0.45)',
                'card-dark': '0 4px 20px rgba(0, 0, 0, 0.5)',
                'panel-dark': '0 8px 32px rgba(0, 0, 0, 0.6)',
                'glow-cyan': '0 0 20px rgba(0, 240, 255, 0.35)',
                'glow-primary-subtle': '0 0 16px rgba(0, 94, 164, 0.25)',
            },

            fontSize: {
                // Thành Khang Figma Typography Scale (595:15360)
                h1: ['3rem', { lineHeight: '1.4', fontWeight: '700' }],           // 48px / 1.4 / 700 (Desktop)
                'h1-mobile': ['2rem', { lineHeight: '1.3', fontWeight: '700' }], // 32px / 1.3 / 700 (Mobile)
                h2: ['1.75rem', { lineHeight: '1.5', fontWeight: '700' }],       // 28px / 1.5 / 700 (Desktop)
                'h2-mobile': ['1.5rem', { lineHeight: '1.4', fontWeight: '700' }], // 24px / 1.4 / 700 (Mobile)
                h3: ['1.5rem', { lineHeight: '1.6', fontWeight: '700' }],        // 24px / 1.6 / 700 (Desktop)
                'h3-mobile': ['1.25rem', { lineHeight: '1.5', fontWeight: '700' }], // 20px / 1.5 / 700 (Mobile)
                h4: ['1.25rem', { lineHeight: '1.6', fontWeight: '700' }],       // 20px / 1.6 / 700 (Desktop)
                'h4-mobile': ['1.125rem', { lineHeight: '1.5', fontWeight: '700' }], // 18px / 1.5 / 700 (Mobile)

                // Body Scale
                'body-xl': ['1.25rem', { lineHeight: '1.6' }],   // 20px / 1.6
                'body-lg': ['1.125rem', { lineHeight: '1.6' }],  // 18px / 1.6
                'body-md': ['1rem', { lineHeight: '1.6' }],      // 16px / 1.6 (default)
                body: ['1rem', { lineHeight: '1.6' }],           // 16px / 1.6 alias
                'body-sm': ['0.875rem', { lineHeight: '1.6' }],  // 14px / 1.6
                'body-xs': ['0.75rem', { lineHeight: '1.6' }],   // 12px / 1.6

                // Figma "PC/Heading" styles used on Liên hệ (939:2577)
                'pc-h3': ['2.25rem', { lineHeight: '1.4', fontWeight: '700' }],  // 36px / 1.4 / 700
                'pc-h5': ['1.75rem', { lineHeight: '1.6', fontWeight: '700' }],  // 28px / 1.6 / 700

                caption: ['14px', { lineHeight: '20px', letterSpacing: '0' }],
                'caption-sm': ['13px', { lineHeight: '16px', letterSpacing: '0', fontWeight: '600' }],
                'caption-xs': ['12px', { lineHeight: '16px', letterSpacing: '0', fontWeight: '600' }],

                'display-hero': ['48px', { lineHeight: '1.4', letterSpacing: '0' }],
                'display-banner': ['32px', { lineHeight: '1.3', letterSpacing: '0' }],
                'display-section': ['28px', { lineHeight: '1.5', letterSpacing: '0' }],
                'display-card': ['24px', { lineHeight: '1.6', letterSpacing: '0' }],
                'display-step': ['42px', { lineHeight: '51px', letterSpacing: '0' }],
                'display-2xl': ['4.5rem', { lineHeight: '5.625rem', letterSpacing: '-0.02em' }], // 72px / 90px
                'display-xl': ['3.75rem', { lineHeight: '4.5rem', letterSpacing: '-0.02em' }],   // 60px / 72px
                'display-lg': ['3rem', { lineHeight: '3.75rem', letterSpacing: '-0.02em' }],      // 48px / 60px
                'display-md': ['2.25rem', { lineHeight: '2.75rem', letterSpacing: '-0.02em' }],   // 36px / 44px
                'display-sm': ['1.875rem', { lineHeight: '2.375rem', letterSpacing: '0' }],       // 30px / 38px
                'display-xs': ['1.5rem', { lineHeight: '2rem', letterSpacing: '0' }],              // 24px / 32px
                'text-xl': ['1.25rem', { lineHeight: '1.875rem', letterSpacing: '0' }],           // 20px / 30px
                'text-lg': ['1.125rem', { lineHeight: '1.75rem', letterSpacing: '0' }],           // 18px / 28px
                'text-md': ['1rem', { lineHeight: '1.5rem', letterSpacing: '0' }],                 // 16px / 24px
                'text-sm': ['0.875rem', { lineHeight: '1.25rem', letterSpacing: '0' }],           // 14px / 20px
                'text-xs': ['0.75rem', { lineHeight: '1.125rem', letterSpacing: '0' }],           // 12px / 18px
            },

            borderRadius: {
                cta: '4px',
                btn: '8px',
                card: '16px',
                input: '10px',
                panel: '20px',
                pill: '9999px',
            },

            typography: (theme) => ({

                DEFAULT: {

                    css: {

                        color: theme('colors.gray.600'),

                        a: {

                            color: theme('colors.primary.DEFAULT'),

                        },

                    },

                },

            }),

            fontFamily: {

                sans: ['"Open Sans"', 'Inter', 'system-ui', 'sans-serif'],
                display: ['"Open Sans"', 'Inter', 'system-ui', 'sans-serif'],
                body: ['"Open Sans"', 'Inter', 'system-ui', 'sans-serif'],
                mono: ['VCR_OSD_MONO', 'monospace'],

            },

            maxWidth: {
                canvas: '1440px',
                container: '1280px',
                'container-wide': '1344px',   // 1280px inner + 2x32px desktop gutter
                'container-narrow': '1216px', // 1152px inner + 2x32px desktop gutter
                content: '1152px',
                hero: '1280px',
                '60vw': '60vw',
                '70vw': '70vw',
                '80vw': '80vw',
                '90vw': '90vw',
            },

            aspectRatio: {
                square: '1 / 1',
            },

        },

    },



    plugins: [

        require('@tailwindcss/typography'),

        require('@tailwindcss/line-clamp'),

    ],

};
