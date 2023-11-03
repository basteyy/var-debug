<?php
/**
 * @author Sebastian Eiweleit <sebastian@eiweleit.de>
 * @website https://github.com/basteyy
 * @website https://eiweleit.de
 */

declare(strict_types=1);

?><html>
<head><title>varDebug</title>
    <meta charset="utf-8">
    <style>
        body {
            background-color: #2D4263;
            color: #EEEEEE;
            padding: 1em;
            font-family: "Ubuntu Light", serif;
            font-weight: lighter;
            max-width: 99vw;
        }

        a {
            color: #C84B31;
        }

        a:hover {
            color: #A13333
        }

        pre {
            overflow: auto;
        }

        label span {
            position: absolute;
            right: 15vw;
        }

        pre:not(.in-table) {
            height: 0;
            margin: 0;
        }

        label span, .copy {
            font-size: .7em;
            float: right;
            font-weight: lighter;
            background-color: #C84B31;
            padding: .2em;
        }

        label span:hover, .copy:hover {
            font-size: .7em;
            float: right;
            font-weight: lighter;
            background-color: #A13333;
            cursor: pointer;
        }

        code {
            display: block;
            padding: 1em;
            border-radius: .4em;
            background-color: #191919;
            font-family: "Ubuntu Mono", monospace;
            font-size: 1.1em;
            line-height: 1.5em;
        }

        pre.in-table {
            margin: 0
        }

        pre.in-table > code {
            padding: 0
        }

        footer {
            padding: 1em;
            text-align: right;
        }

        .accordion {
            overflow: hidden;
            box-shadow: 0 4px 4px -2px rgba(0, 0, 0, 0.5);
            border-radius: 8px;
        }

        .accordion label {
            border-radius-topleft: 8px;
            border-radius-topright: 8px;
            display: flex;
            justify-content: space-between;
            padding: 1em;
            background: #2c3e50;
            font-weight: bold;
            cursor: pointer;
        }

        .accordion label:hover {
            background: #1a252f;
        }

        .accordion label::after {
            content: "❯";
            width: 1em;
            height: 1em;
            text-align: center;
            transition: all 0.35s;
        }

        .accordion input {
            display: none;
        }

        .accordion pre code {
            border-radius: 0;
        }

        .accordion input:checked + label {
            background: #1a252f;
        }

        .accordion input:checked + label::after {
            transform: rotate(90deg);
        }

        .accordion input:checked ~ pre {
            height: auto;
        }

        .copyme:hover {
            cursor: pointer;
            background-color: #A13333;
        }

        td {
            padding: .4em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr {
            margin: 0
        }

        tr:hover td, tr:hover td pre code {
            background: #2c3e50;
        }

        td.title {
            width: 25%;
        }</style>
</head>
<body>
    <div class="accordion">
        <?php
        echo $data_collection; ?>

        <div id="backtrace"><input type="checkbox" id="_backtrace"><label for="_backtrace">Backtrace</label>
            <pre><code><?php
                    debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS); ?></code></pre>
        </div>

    </div>
    <script>
        /** Google Search **/
        document.querySelectorAll("span.google").forEach(function (element, index, array) {
            element.addEventListener("click", function () {
                window.open("https://www.google.com/search?q=" + encodeURI("+php " + document.querySelector("#" + element.getAttribute("data-debug")).innerHTML), "_blank");
            })
        });

        /** Copy text **/
        document.querySelectorAll("span.copyme").forEach(function (element, index, array) {
            element.addEventListener("click", function () {
                navigator.clipboard.writeText(element.innerHTML);
            });
        });
    </script>
    <footer>
        Problems or suggestions? Submit it on <a title="This script on GitHub" href="https://github.com/basteyy/var-debug/">https://github.com/basteyy/var-debug/</a>
    </footer>
</body>
</html>
