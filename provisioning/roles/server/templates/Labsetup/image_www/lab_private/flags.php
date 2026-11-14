<?php
/* Per-sandbox flags, rendered by Ansible from the APG variables.
   Lives outside every DocumentRoot, so Apache never serves it. */
return [
    'task1' => '{{ task1_flag }}',
    'task2' => '{{ task2_flag }}',
    'task3' => '{{ task3_flag }}',
    'task4' => '{{ task4_flag }}',
    'task5' => '{{ task5_flag }}',
    'task6' => '{{ task6_flag }}',
    'task7' => '{{ task7_flag }}',
];
