CREATE DATABASE files;
 
USE files;

CREATE TABLE IF NOT EXISTS files(  
   file_id int(11) primary key auto_increment,  
   file_name varchar(100) not null,
   file_path varchar(200) not null,
   created_at timestamp default now(),  
   updated_at timestamp  
);  

CREATE TABLE IF NOT EXISTS temp_files(  
   temp_file_id int(11) primary key auto_increment,
   original_id int (30) not null,
   temp_file_name varchar(100) not null,
   temp_file_url varchar(200) not null,
   created_at timestamp default now(),  
   updated_at timestamp  
);