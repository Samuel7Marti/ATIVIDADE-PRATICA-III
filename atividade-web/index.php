<?php
$html_resultado = "";

if (isset($_GET['pais']) && !empty($_GET['pais'])) {

    $pais_nome = strtolower(trim($_GET['pais']));

    $options = [
        'http' => [
            'header' => "User-Agent: MeuScriptPHP/1.0\r\n",
            'ignore_errors' => true
        ]
    ];
    $contexto = stream_context_create($options);

    $url_api_en = "https://restcountries.com/v3.1/name/" . urlencode($pais_nome);
    $json_data = @file_get_contents($url_api_en, false, $contexto);

    if ($json_data === FALSE || strpos($http_response_header[0], "404")) {
        
        $url_api_pt = "https://restcountries.com/v3.1/translation/" . urlencode($pais_nome);
        $json_data = @file_get_contents($url_api_pt, false, $contexto);
    }
    
    if ($json_data === FALSE || strpos($http_response_header[0], "404")) {
        
        $html_resultado = "<h2>Erro ao buscar dados. O país '$pais_nome' não foi encontrado.</h2>";
    
    } else {
        
        $dados_api = json_decode($json_data, true);
        $info_pais = $dados_api[0];

        $nome_comum = $info_pais['name']['common'];
        $capital = $info_pais['capital'][0] ?? 'N/A'; 
        $regiao = $info_pais['region'];
        $populacao = $info_pais['population'];
        $flag_url = $info_pais['flags']['png'];
        $flag_alt = $info_pais['flags']['alt'] ?? "Bandeira do $nome_comum";

        $html_resultado = "
            <img src='$flag_url' alt='$flag_alt'> 
            <h2>Informações sobre: $nome_comum</h2>
            <p><strong>Capital:</strong> $capital</p>
            <p><strong>Região:</strong> $regiao</p>
            <p><strong>População:</strong> " . number_format($populacao, 0, ',', '.') . " habitantes</p>
        ";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Países - Atividade III</title>
    <style>
        :root {
            --bg-color: #f4f7f6;
            --card-bg-color: #ffffff;
            --text-color: #212529;
            --text-muted: #555;
            --border-color: #dee2e6;
            --primary-color: #28a745;
            --primary-hover-color: #218838;
            --shadow-color: rgba(0,0,0,0.05);
        }

        body.dark-mode {
            --bg-color: #1a1a1a;
            --card-bg-color: #2c2c2c;
            --text-color: #f0f0f0;
            --text-muted: #aaa;
            --border-color: #444;
            --primary-color: #28a745;
            --primary-hover-color: #34c759;
            --shadow-color: rgba(0,0,0,0.2);
        }

        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; 
            max-width: 600px; 
            margin: 40px auto; 
            padding: 20px;
            background-color: var(--bg-color);
            color: var(--text-color);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            transition: background-color 0.3s, color 0.3s;
            box-shadow: 0 4px 12px var(--shadow-color);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        h1, h2 { 
            color: var(--text-color); 
            margin: 0;
        }
        h1 { font-size: 28px; }
        h2 { font-size: 24px; }
        
        label {
            color: var(--text-muted);
            font-weight: 500;
        }

        form { 
            margin-bottom: 25px; 
            background-color: var(--card-bg-color);
            padding: 20px;
            border-radius: 8px; 
            border: 1px solid var(--border-color);
            transition: background-color 0.3s;
        }

        input[type="text"] { 
            padding: 12px; 
            width: calc(70% - 24px);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-size: 16px;
        }

        button[type="submit"] { 
            padding: 12px 20px; 
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        button[type="submit"]:hover {
            background-color: var(--primary-hover-color);
        }

        #resultado { 
            background-color: var(--card-bg-color); 
            padding: 20px;
            border-radius: 8px; 
            border: 1px solid var(--border-color);
            min-height: 50px;
            transition: background-color 0.3s;
        }
        
        #resultado img {
            max-width: 100px;
            height: auto;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            margin-bottom: 15px;
        }

        #theme-toggle {
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            line-height: 1;
            color: var(--text-color);
        }
        #theme-toggle:hover {
            background-color: var(--border-color);
        }
        
    </style>
</head>
<body>

    <div class="header-container">
        <h1>Busca de Países</h1>
        <button id="theme-toggle">🌙</button>
    </div>

    <form action="" method="GET">
        <label for="pais">Digite o nome do país (PT ou EN):</label>
        <br><br>
        <input type="text" id="pais" name="pais" placeholder="Ex: Brasil, Germany, Japão">
        <button type="submit">Buscar</button>
    </form>

    <div id="resultado">
        <?php echo $html_resultado; ?>
    </div>

    <script>
        const toggleButton = document.getElementById('theme-toggle');
        const body = document.body;

        function setTheme(theme) {
            if (theme === 'dark') {
                body.classList.add('dark-mode');
                toggleButton.textContent = '☀️';
                localStorage.setItem('theme', 'dark');
            } else {
                body.classList.remove('dark-mode');
                toggleButton.textContent = '🌙';
                localStorage.setItem('theme', 'light');
            }
        }

        toggleButton.addEventListener('click', () => {
            const isDarkMode = body.classList.contains('dark-mode');
            setTheme(isDarkMode ? 'light' : 'dark');
        });

        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
        });
    </script>

</body>
</html>