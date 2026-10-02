-- loyalty_prompting_and_other_prompts --
ALTER TABLE `phppos_locations` 
ADD `blockchyp_prompt_for_loyalty` INT(1) DEFAULT '0', 
ADD `blockchyp_prompt_for_name` INT(1) DEFAULT '0',
ADD `blockchyp_prompt_for_email` INT(1) DEFAULT '0',
ADD `blockchyp_prompt_for_phone_number` INT(1) DEFAULT '0',
ADD `blockchyp_ask_for_missing_info` INT(1) DEFAULT '0';