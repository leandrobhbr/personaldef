<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dados de contato
    |--------------------------------------------------------------------------
    |
    | Valores exibidos no site. Os textos entre colchetes são marcadores para
    | serem trocados pelos dados reais (via .env ou direto aqui).
    |
    */

    'whatsapp' => env('SITE_WHATSAPP', '5514996295320'),
    'whatsapp_display' => env('SITE_WHATSAPP_DISPLAY', '(14) 99629-5320'),
    'whatsapp_message' => 'Olá Denilson!',

    // Modal do WhatsApp: "O que você procura?" (a chave vai em data-wa-tipo nos botões)
    'whatsapp_opcoes' => [
        // [título, subtítulo, ícone, texto que vai na mensagem]
        'presencial' => ['Personal Trainer presencial', 'Atendimento individual em Marília/SP', 'local', 'Personal Trainer presencial em Marília/SP'],
        'online' => ['Assessoria esportiva online', 'Com criação de fichas personalizadas · todo o Brasil', 'celular', 'Assessoria esportiva online com criação de fichas'],
    ],
    'instagram' => env('SITE_INSTAGRAM', 'https://www.instagram.com/blackdef79/'),
    'instagram_display' => env('SITE_INSTAGRAM_DISPLAY', '@blackdef79'),
    'city' => 'Marília/SP',
    'domain' => 'personaldef.com.br',

    // Cor de destaque do layout (o mockup também testou #FF7A1A, #C6FF00 e #FFD400)
    'accent' => '#E4FF3A',

    // Quem atende
    'owner_name' => 'Denilson Ferreira',
    'cref' => '201019-G/SP',
    'owner_role' => 'Personal Trainer - CREF 201019-G/SP',
    // Foto do hero (vazio = mostra o espaço reservado do layout); precisa de boa resolução (mín. ~900x1100)
    'owner_photo' => 'images/denilson.webp',

    // Google Analytics 4 (só é carregado com APP_ENV=production)
    'ga_id' => env('SITE_GA_ID'),

    // Senha da página /previa, que mostra os depoimentos de exemplo só para quem a digitar (vazio = desligada)
    'previa_senha' => env('SITE_PREVIA_SENHA'),

    // Código de verificação do Google Search Console (meta tag), opcional
    'google_verification' => env('SITE_GOOGLE_VERIFICATION'),

    /*
    |--------------------------------------------------------------------------
    | Formação (seção Sobre)
    |--------------------------------------------------------------------------
    */

    // Diplomas (fotos tratadas em public/images/diplomas), abertos em slide pelos cartões de formação
    'diplomas' => [
        'orthos-musculacion' => ['Monitor de Musculação', 'Orthos · Barcelona, 2008'],
        'orthos-fitness' => ['Monitor de Fitness', 'Orthos · Barcelona, 2008'],
        'entrenador-personal' => ['Personal Trainer (Entrenador Personal)', 'Life Santé · Sevilha'],
        'culturismo-musculacion' => ['Professor e Treinador de Culturismo e Musculação', 'Life Santé · Sevilha'],
        'life-pilates' => ['Monitor de Pilates', 'Life Santé · Sevilha, 2009'],
        'cardiobox' => ['Monitor de Cardiobox', 'Life Santé · Sevilha'],
        'aerobic-step' => ['Técnico em Aeróbica e Step', 'Life Santé · Sevilha'],
        'dietoterapia' => ['Técnico em Dietoterapia e Suplementação Natural', 'Life Santé · Sevilha'],
        'fitoterapia' => ['Fitoterapia Aplicada ao Esporte', 'Life Santé · Sevilha'],
        'naturopatia' => ['Naturopatia', 'Life Santé · Sevilha'],
    ],

    'formacao' => [
        [
            'icon' => 'halter',
            'tag' => 'Especialista Internacional',
            'title' => 'Musculação e Cardio Fitness',
            'where' => 'Orthos Barcelona · IFAD Portugal',
            'diplomas' => ['orthos-musculacion', 'orthos-fitness', 'entrenador-personal'],
        ],
        [
            'icon' => 'folha',
            'tag' => 'Formação complementar',
            'title' => 'Técnico Experto em Dietoterapia, Nutrição Esportiva e Dietética',
            'where' => 'Sevilla e Valência · Espanha',
            'diplomas' => ['dietoterapia'],
        ],
        [
            'icon' => 'coracao',
            'tag' => 'Terapias e Bem-Estar',
            'title' => 'Quiromassagem · Naturopatia e Fitoterapia Aplicada',
            'where' => 'Escola de Enfermagem de Valência · Espanha',
            'diplomas' => ['naturopatia', 'fitoterapia'],
        ],
        [
            'icon' => 'trofeu',
            'tag' => 'Modalidades',
            'title' => 'Treinador Culturista · Pilates · Cardiobox · Step',
            'where' => 'Fisiculturismo e monitoria · Espanha',
            'diplomas' => ['culturismo-musculacion', 'life-pilates', 'cardiobox', 'aerobic-step'],
        ],
    ],

    // Fotos do Denilson na Espanha (próprias; se usar foto de banco, preencha author/source/license para o crédito)
    'europa' => [
        [
            'image' => 'images/europa/denilson-espanha-mureta.webp',
            'alt' => 'Denilson Ferreira sentado numa mureta em rua histórica da Espanha, em 2008',
            'country' => 'Espanha',
            'city' => '2008',
        ],
        [
            'image' => 'images/europa/denilson-espanha-catedral.webp',
            'alt' => 'Denilson Ferreira diante de uma igreja histórica na Espanha, durante a formação',
            'country' => 'Espanha',
            'city' => 'Formação na Catalunha',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Serviços e planos
    |--------------------------------------------------------------------------
    */

    'servicos' => [
        [
            'icon' => 'celular',
            'badge' => 'Todo o Brasil',
            'title' => 'Consultoria de Treino Online',
            'text' => 'Fichas de treino montadas do zero para você, entregues em formato digital e explicativo. Treine onde estiver, no seu horário, com a segurança de saber exatamente o que fazer.',
            'items' => [
                'Treino 100% individualizado',
                'Séries, repetições, descanso e execução passo a passo',
                'Praticidade: academia, condomínio ou em casa',
            ],
            'cta' => 'Solicitar orçamento',
            'tipo' => 'online',
            'message' => 'Olá Denilson! Quero solicitar um orçamento da Consultoria de Treino Online.',
        ],
        [
            'icon' => 'local',
            'badge' => 'Marília/SP',
            'title' => 'Personal Trainer Presencial',
            'text' => 'Atendimento exclusivo, lado a lado, com correção técnica em cada repetição. Para quem quer o máximo de atenção, segurança e resultado em Marília/SP.',
            'items' => [
                'Acompanhamento individual em cada sessão',
                'Correção de postura e execução em tempo real',
                'Treino, hábitos saudáveis e bem-estar integrados',
            ],
            'cta' => 'Solicitar orçamento',
            'tipo' => 'presencial',
            'message' => 'Olá Denilson! Quero solicitar um orçamento do Personal Trainer Presencial em Marília.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Como funciona a consultoria
    |--------------------------------------------------------------------------
    */

    'passos' => [
        ['Chame no WhatsApp', 'Nosso assistente agenda automaticamente sua conversa inicial, no horário que for melhor para você.'],
        ['Avaliação completa', 'Mapeamos sua rotina, histórico, limitações, lesões, equipamentos disponíveis e o objetivo que você quer alcançar.'],
        ['Ficha montada do zero', 'Denilson desenha seu plano com a metodologia europeia — nada de modelo pronto ou ficha genérica.'],
        ['Receba e treine', 'Sua ficha chega em formato digital e explicativo, pronta para usar no celular durante o treino.'],
    ],

    'ficha' => [
        ['halter', 'Nome de cada exercício'],
        ['lista', 'Séries e repetições'],
        ['relogio', 'Tempo de descanso'],
        ['execucao', 'Execução passo a passo'],
    ],

    // Ficha de exemplo mostrada no celular da seção "Como funciona"
    'ficha_exemplo' => [
        'treino' => 'Treino A',
        'foco' => 'Peito e tríceps',
        'semana' => 'Semana 3 de 8',
        'exercicios' => [
            ['Supino reto com barra', 4, '8–10', '90s', null],
            ['Supino inclinado com halteres', 3, '10–12', '75s', null],
            ['Crucifixo na máquina', 3, '12', '60s', 'Cotovelos levemente flexionados; feche os braços em 2 segundos e volte devagar, sem deixar o peso bater.'],
            ['Tríceps na polia com corda', 3, '12–15', '60s', null],
            ['Tríceps francês com halter', 3, '10–12', '60s', null],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de ficha de treino
    |--------------------------------------------------------------------------
    |
    | Cada dia: [rótulo, grupos, foto, enquadramento (object-position)].
    | Fotos do Wikimedia Commons; créditos em 'fichas_creditos'.
    |
    */

    'tipos_ficha' => [
        [
            'title' => 'Full Body',
            'subtitle' => 'Corpo inteiro',
            'days' => [
                ['Todos', 'Corpo todo', 'images/fichas/full-body.webp', '50% 35%'],
            ],
            'what' => 'Todos os grandes grupos musculares no mesmo treino.',
            'freq' => '2 a 3 vezes por semana',
            'who' => 'Iniciantes, quem tem pouco tempo ou está voltando a treinar.',
        ],
        [
            'title' => 'AB',
            'subtitle' => 'Superior e inferior (ou Push/Pull)',
            'days' => [
                ['A', 'Superiores', 'images/fichas/superiores.webp', '50% 30%'],
                ['B', 'Inferiores', 'images/fichas/inferiores.webp', '50% 25%'],
            ],
            'what' => 'Superiores em um dia e inferiores no outro (ou empurrar e puxar).',
            'freq' => '2 a 4 vezes por semana',
            'who' => 'Iniciantes em evolução e intermediários com rotina apertada.',
        ],
        [
            'title' => 'ABC',
            'subtitle' => 'Três treinos diferentes',
            'days' => [
                ['A', 'Peito e tríceps', 'images/fichas/peito-triceps.webp', '50% 40%'],
                ['B', 'Costas e bíceps', 'images/fichas/costas-biceps.webp', '50% 40%'],
                ['C', 'Pernas e ombros', 'images/fichas/pernas-ombros.webp', '40% 50%'],
            ],
            'what' => 'Três treinos diferentes, como peito e tríceps, costas e bíceps, pernas e ombros.',
            'freq' => '3 a 5 vezes por semana',
            'who' => 'Intermediários com foco em hipertrofia.',
        ],
        [
            'title' => 'Push / Pull / Legs',
            'subtitle' => 'Empurrar / Puxar / Pernas',
            'days' => [
                ['Push', 'Peito, ombro, tríceps', 'images/fichas/push.webp', '50% 60%'],
                ['Pull', 'Costas e bíceps', 'images/fichas/pull.webp', '50% 40%'],
                ['Legs', 'Pernas completas', 'images/fichas/legs.webp', '45% 60%'],
            ],
            'what' => 'Treinos pelo tipo de movimento: empurrar, puxar e pernas.',
            'freq' => '3 a 6 vezes por semana',
            'who' => 'Intermediários e avançados que querem otimizar o descanso muscular.',
        ],
        [
            'title' => 'ABCD ou ABCDE',
            'subtitle' => 'Um grupo por dia',
            'days' => [
                ['A', 'Peito', 'images/fichas/peito.webp', '50% 60%'],
                ['B', 'Costas', 'images/fichas/costas.webp', '50% 40%'],
                ['C', 'Ombros', 'images/fichas/ombros.webp', '50% 30%'],
                ['D', 'Braços', 'images/fichas/bracos.webp', '35% 40%'],
                ['E', 'Pernas', 'images/fichas/pernas.webp', '55% 60%'],
            ],
            'what' => 'Um grupo muscular por dia: peito, costas, ombros, braços e pernas.',
            'freq' => '4 a 6 vezes por semana',
            'who' => 'Intermediários e avançados que treinam com alta intensidade.',
        ],
    ],

    // Créditos das fotos dos tipos de ficha (CC BY / CC BY-SA exigem citar o autor e a licença)
    'fichas_creditos' => [
        ['Nenad Stojkovic', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0/deed.pt-br'],
        ['Tyler Read', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0/deed.pt-br'],
        ['Abooyeah', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0/deed.pt-br'],
        ['David Shankbone', 'CC BY-SA 3.0', 'https://creativecommons.org/licenses/by-sa/3.0/deed.pt-br'],
        ['OliverSeitz, U.S. Air Force e U.S. Marine Corps', 'domínio público', null],
    ],

    /*
    |--------------------------------------------------------------------------
    | Saúde: condições que exigem ficha individualizada
    |--------------------------------------------------------------------------
    |
    | Cada grupo: foto, enquadramento e as condições [nome, o que a ficha precisa].
    |
    */

    'saude' => [
        [
            'title' => 'Condições musculoesqueléticas e articulares',
            'image' => 'images/saude/musculoesqueleticas.webp',
            'pos' => '50% 50%',
            'intro' => 'Exercícios, amplitude e carga ajustados para proteger as articulações.',
            'items' => [
                ['Hérnia de disco', 'sem compressão na coluna; foco no core.'],
                ['Artrose e artrite', 'baixo impacto e amplitude sem dor.'],
                ['Escoliose', 'exercícios unilaterais contra assimetrias.'],
                ['Tendinites e ligamentos', 'transição segura da fisioterapia.'],
            ],
        ],
        [
            'title' => 'Doenças cardiovasculares e metabólicas',
            'image' => 'images/saude/cardiovasculares.webp',
            'pos' => '50% 45%',
            'intro' => 'Pressão e frequência cardíaca sob controle em cada série.',
            'items' => [
                ['Hipertensão', 'sem prender a respiração; pausas adequadas.'],
                ['Diabetes', 'treino alinhado à alimentação e à medicação.'],
                ['Cardiopatias', 'limite de frequência cardíaca e progressão gradual.'],
            ],
        ],
        [
            'title' => 'Sobrecarga mecânica',
            'image' => 'images/saude/sobrecarga.webp',
            'pos' => '50% 50%',
            'intro' => 'Quando o próprio peso já sobrecarrega as articulações.',
            'items' => [
                ['Obesidade grave', 'sem impacto; aparelhos e posições adaptados.'],
            ],
        ],
        [
            'title' => 'Condições sistêmicas e autoimunes',
            'image' => 'images/saude/sistemicas.webp',
            'pos' => '50% 40%',
            'intro' => 'Tolerância ao esforço variável e movimentos contraindicados.',
            'items' => [
                ['Fibromialgia', 'volume reduzido e intensidade moderada.'],
                ['Osteoporose', 'sem torções bruscas; carga que fortalece os ossos.'],
            ],
        ],
    ],

    'saude_creditos' => [
        ['Shixart1985', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0/deed.pt-br'],
        ['Gary Bembridge', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0/deed.pt-br'],
        ['YitzhakNat', 'CC0', null],
        ['Bill Branson', 'domínio público', null],
    ],

    /*
    |--------------------------------------------------------------------------
    | Depoimentos (home)
    |--------------------------------------------------------------------------
    |
    | texto, nome, contexto, foto (opcional; só com autorização da pessoa, em
    | public/images/depoimentos; sem foto, mostra as iniciais) e fotos (opcional:
    | abertas em slide pelo botão "Veja fotos").
    | 'exemplo' => true: nomes e avatares fictícios, só para ver o layout no
    | ambiente local; NUNCA aparecem em produção. Troque pelos depoimentos reais.
    |
    */

    'depoimentos' => [
        ['exemplo' => true, 'texto' => 'Em 5 meses de consultoria online perdi 9 kg sem largar a rotina. O Denilson ajusta a ficha toda semana e responde rápido pelo WhatsApp.', 'nome' => 'Camila Rocha', 'contexto' => 'Consultoria online · Curitiba/PR', 'foto' => 'images/depoimentos/exemplo-1.svg', 'fotos' => ['images/blog/dieta-rotina.webp', 'images/fichas/pernas.webp', 'images/academia.webp']],
        ['exemplo' => true, 'texto' => 'Voltei a treinar depois de uma lesão no joelho. Com o Denilson do lado corrigindo cada execução, ganhei força sem dor nenhuma.', 'nome' => 'Rafael Mendes', 'contexto' => 'Personal presencial · Marília/SP', 'foto' => 'images/depoimentos/exemplo-2.svg', 'fotos' => ['images/academia.webp', 'images/fichas/inferiores.webp', 'images/saude/musculoesqueleticas.webp', 'images/blog/erros-hipertrofia.webp']],
        ['exemplo' => true, 'texto' => 'Tenho pressão alta e medo de exagerar. A ficha individualizada do Denilson me deu segurança, e hoje treino 4 vezes por semana.', 'nome' => 'Sônia Prado', 'contexto' => 'Ficha de saúde · Marília/SP', 'foto' => 'images/depoimentos/exemplo-3.svg', 'fotos' => ['images/saude/cardiovasculares.webp', 'images/saude/sistemicas.webp', 'images/saude/sobrecarga.webp', 'images/blog/dor-nas-costas.webp']],
    ],

    /*
    |--------------------------------------------------------------------------
    | Blog
    |--------------------------------------------------------------------------
    */

    'posts_creditos' => [
        ['Abooyeah', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0/deed.pt-br'],
        ['Ella Olsson', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0/deed.pt-br'],
        ['Nenad Stojkovic', 'CC BY 2.0', 'https://creativecommons.org/licenses/by/2.0/deed.pt-br'],
    ],

    'posts' => [
        [
            'title' => '3 erros que travam a hipertrofia',
            'image' => 'images/blog/erros-hipertrofia.webp',
            'category' => 'Treino',
            'reading' => 5,
            'excerpt' => 'Volume mal distribuído, descanso errado e falta de progressão: veja como destravar seu ganho de massa.',
        ],
        [
            'title' => 'Como manter a alimentação saudável na rotina corrida',
            'image' => 'images/blog/dieta-rotina.webp',
            'category' => 'Hábitos',
            'reading' => 4,
            'excerpt' => 'Planejamento simples, marmitas práticas e escolhas fora de casa que não sabotam seu treino. Para um plano alimentar, procure um nutricionista.',
        ],
        [
            'title' => 'Treinar com dor nas costas: o que fazer (e evitar)',
            'image' => 'images/blog/dor-nas-costas.webp',
            'category' => 'Saúde',
            'reading' => 6,
            'excerpt' => 'Como adaptar o treino a limitações e fortalecer a região com segurança e orientação profissional.',
        ],
    ],

];
