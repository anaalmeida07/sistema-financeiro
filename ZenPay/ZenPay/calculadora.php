<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora</title>
    <link rel="stylesheet" href="css/calculadora.css">
</head>
<body>
    <div class="calculator">
        <input type="text" id="display" disabled>
        <div class="buttons">
            <button onclick="clearDisplay()">C</button>
            <button onclick="deleteLast()">⌫</button>
            <button onclick="insert('/')">÷</button>
            <button onclick="insert('*')">×</button>
            <button onclick="insert('7')">7</button>
            <button onclick="insert('8')">8</button>
            <button onclick="insert('9')">9</button>
            <button onclick="insert('-')">−</button>
            <button onclick="insert('4')">4</button>
            <button onclick="insert('5')">5</button>
            <button onclick="insert('6')">6</button>
            <button onclick="insert('+')">+</button>
            <button onclick="insert('1')">1</button>
            <button onclick="insert('2')">2</button>
            <button onclick="insert('3')">3</button>
            <button onclick="calculate()">=</button>
            <button onclick="insert('0')" class="zero">0</button>
            <button onclick="insert('.')">.</button>
        </div>
    </div>
    <script>
        const display = document.getElementById('display');

        function insert(value) {
            display.value += value;
        }

        function clearDisplay() {
            display.value = '';
        }

        function deleteLast() {
            display.value = display.value.slice(0, -1);
        }

        function calculate() {
            try {
                display.value = eval(display.value);
            } catch {
                display.value = 'Erro';
            }
        }
    </script>
</body>
</html>
