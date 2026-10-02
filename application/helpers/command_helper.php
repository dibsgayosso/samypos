<?php
function run_command_in_background($command)
{
	shell_exec($command.' >/dev/null 2>/dev/null &');
}