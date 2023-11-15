<?php

const DB_HOST_2 = "";
const DB_LOGIN_2 = "";
const DB_PASS_2 = "";
const DB_NAME_2 = "";

$link1 = mysqli_connect(DB_HOST_2, DB_LOGIN_2, DB_PASS_2, DB_NAME_2);

if (mysqli_connect_errno()) {
    printf("Connection established: %s\n", mysqli_connect_error());
    exit();
}

mysqli_query($link1, "set names utf8");