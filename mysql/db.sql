CREATE DATABASE files;
 
USE files;

CREATE TABLE IF NOT EXISTS files(  
   file_id int(11) primary key auto_increment,  
   file_name varchar(100) not null,
   file_path varchar(200) not null,
   created_at timestamp default now(),  
   updated_at timestamp  
);  