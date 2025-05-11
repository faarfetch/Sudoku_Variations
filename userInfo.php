<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Statistics with Charts</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="styles.css">
</head>

<body class="bg-gray-100 p-6 text-gray-800">
    <?php 
    include 'header.php';
    ?>
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow space-y-8">
        <?php
        require_once 'gestori/gestoreDatabase.php';
        if (!isset($_SESSION)) {
            session_start();
        }
        $username = $_SESSION['username'];

        $gestore = GestoreDatabase::getInstance();
        $statistiche = $gestore->getStatistichePerTipo($username);

        function aggregaStatistiche($data, $tipo, $dimensione)
        {
            $difficolta = ['easy', 'medium', 'hard'];
            $completati = 0;
            $migliorTempo = PHP_INT_MAX;
            $tempoTotale = 0;
            $erroriTotali = 0;
            $conteggio = 0;

            foreach ($difficolta as $diff) {
                if (isset($data[$tipo][$diff][$dimensione])) {
                    $entry = $data[$tipo][$diff][$dimensione];
                    $completati += $entry['completati'];
                    $migliorTempo = min($migliorTempo, $entry['migliorTempo']);
                    $tempoTotale += $entry['tempoMedio'] * $entry['completati'];
                    $erroriTotali += $entry['erroriMedi'] * $entry['completati'];
                    $conteggio += $entry['completati'];
                }
            }

            $tempoMedio = $conteggio > 0 ? round($tempoTotale / $conteggio, 2) : 0;
            $erroriMedi = $conteggio > 0 ? round($erroriTotali / $conteggio, 2) : 0;

            return [
                'completati' => $completati,
                'migliorTempo' => $migliorTempo === PHP_INT_MAX ? 0 : $migliorTempo,
                'tempoMedio' => $tempoMedio,
                'erroriMedi' => $erroriMedi
            ];
        }

        $tipi = ['Normal', 'City', 'Pois'];
        $dimensioni = ['9x9', '16x16'];
        ?>

        <div>
            <label class="block text-xl font-semibold mb-2" for="username">Name:</label>
            <input id="username" type="text" value="<?php echo htmlspecialchars($username); ?>"
                class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <h2 class="text-2xl font-bold">Sudoku Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
            // 9x9 cards first
            foreach ($tipi as $tipo) {
                $stat = aggregaStatistiche($statistiche, $tipo, '9x9');
                echo "<div class='bg-gray-50 p-4 rounded-lg shadow-md'>";
                echo "<h3 class='text-xl font-semibold text-blue-700 mb-2'>{$tipo} Sudoku 9x9</h3>";
                echo "<p><strong>Best Time:</strong> {$stat['migliorTempo']}s</p>";
                echo "<p><strong>Avg Time:</strong> {$stat['tempoMedio']}s</p>";
                echo "<p><strong>Avg Errors:</strong> {$stat['erroriMedi']}</p>";
                echo "<p><strong>Completed:</strong> {$stat['completati']}</p>";
                echo "</div>";
            }

            // then 16x16 cards
            foreach ($tipi as $tipo) {
                $stat = aggregaStatistiche($statistiche, $tipo, '16x16');
                echo "<div class='bg-gray-50 p-4 rounded-lg shadow-md'>";
                echo "<h3 class='text-xl font-semibold text-blue-700 mb-2'>{$tipo} Sudoku 16x16</h3>";
                echo "<p><strong>Best Time:</strong> {$stat['migliorTempo']}s</p>";
                echo "<p><strong>Avg Time:</strong> {$stat['tempoMedio']}s</p>";
                echo "<p><strong>Avg Errors:</strong> {$stat['erroriMedi']}</p>";
                echo "<p><strong>Completed:</strong> {$stat['completati']}</p>";
                echo "</div>";
            }
            ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">Sudoku Completati</h3>
                <canvas id="barChart"></canvas>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">Tempo di Completamento Medio (sec)</h3>
                <canvas id="avgTimeChart"></canvas>
            </div>
        </div>

        <script>
            const barChartData = {
                labels: ['Easy', 'Medium', 'Hard'],
                datasets: [
                    <?php foreach ($tipi as $tipo): ?> {
                            label: '<?php echo $tipo; ?>',
                            data: [
                                <?php foreach (['easy', 'medium', 'hard'] as $diff):
                                    $value = isset($statistiche[$tipo][$diff]['9x9']['completati']) ? $statistiche[$tipo][$diff]['9x9']['completati'] : 0;
                                    echo "$value, ";
                                endforeach; ?>
                            ],
                            backgroundColor: '<?php echo match ($tipo) {
                                                    'Normal' => "rgba(59, 130, 246, 0.5)",
                                                    'City' => "rgba(16, 185, 129, 0.5)",
                                                    'Pois' => "rgba(234, 88, 12, 0.5)"
                                                }; ?>'
                        },
                    <?php endforeach; ?>
                ]
            };

            new Chart(document.getElementById('barChart').getContext('2d'), {
                type: 'bar',
                data: barChartData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });

            const radarLabels = <?php echo json_encode($tipi); ?>;
            const radarData = [
                <?php
                foreach ($tipi as $tipo) {
                    $stat = aggregaStatistiche($statistiche, $tipo, '9x9');
                    echo $stat['tempoMedio'] . ',';
                }
                ?>
            ];

            new Chart(document.getElementById('avgTimeChart').getContext('2d'), {
                type: 'radar',
                data: {
                    labels: radarLabels,
                    datasets: [{
                        label: 'Average Time (sec)',
                        data: radarData,
                        backgroundColor: 'rgba(99, 102, 241, 0.2)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        pointBackgroundColor: 'rgba(99, 102, 241, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        r: {
                            suggestedMin: 0
                        }
                    }
                }
            });
        </script>
    </div>
</body>

</html>
