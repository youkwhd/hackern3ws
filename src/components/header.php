<?php

function render_header() {
    return <<< HTML
        <header>
            <h1 class="hn--header-title">
                <a href="/">
                    hackern3ws
                </a>
            </h1>
            <!--
            <nav>
                <ul>
                    <li>
                        <a href="/news">
                            news
                        </a>
                    </li>
                    <li>
                        <a href="/jobs">
                            jobs
                        </a>
                    </li>
                </ul>
            </nav>
            -->
        </header>
    HTML;
}
