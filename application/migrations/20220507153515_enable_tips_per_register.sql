-- enable_tips_per_register --
ALTER TABLE phppos_registers ADD COLUMN enable_tips INT(1) DEFAULT '0';