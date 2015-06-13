drop table Review cascade constraints;
drop table CoopCompany cascade constraints;
drop table CompanyType cascade constraints;
drop table CoopStudent cascade constraints;
drop table Admin cascade constraints;
drop table PositionForCompany cascade constraints;
drop table Skills cascade constraints;
drop table PositionRequiresSkill cascade constraints;
drop table Department cascade constraints;
drop table CompanyHiresForDept cascade constraints;
drop table Location cascade constraints;
drop table CompanyLocation cascade constraints;

CREATE TABLE CompanyType
(type varchar(50) PRIMARY KEY,
description varchar(100) NOT NULL);

CREATE TABLE Department
(name varchar(50) PRIMARY KEY);

CREATE TABLE Admin
(id integer PRIMARY KEY,
name varchar(50) NOT NULL,
email varchar(50) NOT NULL);

CREATE TABLE Skills
(name varchar(50) PRIMARY KEY,
description varchar(100) NOT NULL);

CREATE TABLE Location
(city varchar(50) NOT NULL,
province varchar(50) NOT NULL,
country varchar(50) NOT NULL,
PRIMARY KEY (city, province));

CREATE TABLE  CoopCompany
(name varchar(50) PRIMARY KEY,
about varchar(100) NOT NULL,
type varchar(50) NOT NULL,
FOREIGN KEY (type) REFERENCES CompanyType(type));

CREATE TABLE CoopStudent
( id integer not null PRIMARY KEY,
name varchar(250) not null,
email varchar(250) not null,
year integer not null,
dname varchar(50) not null,
FOREIGN KEY (dname) references Department(name));

CREATE TABLE PositionForCompany
( title varchar(50) not null PRIMARY KEY,
cname varchar(50) not null,
duties varchar(500) not null,
city varchar(50) not null,
province varchar(50) not null,
FOREIGN KEY (cname) references CoopCompany(name),
FOREIGN KEY (city, province) references Location(city, province));

CREATE TABLE Review
( rid integer not null PRIMARY KEY,
review_comment varchar(500) not null,
review_date varchar(50) not null,
companyname varchar(250),
coopstudid integer not null,
postitle varchar(50),
rating integer not null,
FOREIGN KEY (companyname) references CoopCompany(name),
FOREIGN KEY (coopstudid) references CoopStudent(id),
FOREIGN KEY (postitle) references PositionForCompany(title));

CREATE TABLE PositionRequiresSkill
( ptitle varchar(50) not null,
cname varchar(250) not null,
sname varchar(250) not null,
PRIMARY KEY (ptitle, cname, sname),
FOREIGN KEY (ptitle) references PositionForCompany(title),
FOREIGN KEY (cname) references CoopCompany(name),
FOREIGN KEY (sname) references Skills(name));

CREATE TABLE CompanyHiresForDept
(cname varchar(50) NOT NULL,
dname varchar(50) NOT NULL,
PRIMARY KEY (cname, dname), 
FOREIGN KEY (cname) REFERENCES CoopCompany(name),
FOREIGN KEY (dname) REFERENCES Department(name));

CREATE TABLE CompanyLocation
(cname varchar(50) NOT NULL,
city varchar(50) NOT NULL, 
province varchar(50) NOT NULL,
PRIMARY KEY (cname, city, province),
FOREIGN KEY (cname) REFERENCES CoopCompany(name),
FOREIGN KEY (city, province) REFERENCES Location(city, province));


insert into CompanyType values
('Agriculture', 'This industry is focused on food and plants.');

insert into CompanyType values
('Computer Science', 'This industry is focused on programming, etc.');

insert into CompanyType values
('Insurance', 'This industry is focused on providing different types of insurance.');

insert into CompanyType values
('Accounting', 'This industry is focused on keeping track of expenses and revenue.');

insert into CompanyType values
('Energy', 'This industry includes companies such as oil and gas.');


insert into Department values
('Computer Science');

insert into Department values
('Engineering');

insert into Department values
('Math');

insert into Department values
('English');

insert into Department values
('Accounting');

insert into Department values
('Statistics');


insert into Admin values
(101, 'Ada', 'ada@core.com');

insert into Admin values
(102, 'Betty', 'betty@gmail.com');

insert into Admin values
(103, 'Cooper', 'coop@core.com');

insert into Admin values
(104, 'Diana', 'didi@hotmail.com');

insert into Admin values
(105, 'Eliza', 'liza@core.com');


insert into Skills values
('C++', 'know how to code in C++');

insert into Skills values
('Java', 'know how to code in Java');

insert into Skills values
('Python', 'know how to code in Python');

insert into Skills values
('Threading', 'know how to program with threads');

insert into Skills values
('Time management', 'know how to manage your time');


insert into Location values
('Victoria', 'BC', 'Canada');

insert into Location values
('Vancouver', 'BC', 'Canada');

insert into Location values
('Silicon Valley', 'San Francisco', 'USA');

insert into Location values
('Seattle', 'Washington', 'USA');

insert into Location values
('Calgary', 'Alberta', 'Canada');


insert into CoopCompany values
('HootSuite', 'We help link up all the social stuff.', 'Computer Science');

insert into CoopCompany values
('TheFamousApp', 'We created a famous app known all over the world.', 'Computer Science');

insert into CoopCompany values
('Microsoft', 'This company created one of the biggest operating systems in the world.', 'Computer Science');

insert into CoopCompany values
('The Web Devers', 'This company was founded in 2010 to create web apps.', 'Computer Science');

insert into CoopCompany values
('The Inputters', 'We are a company that specializes in inputting data.', 'Computer Science');

insert into CoopCompany values
('The Chicken Farmers', 'We raise chickens!', 'Agriculture');

insert into CoopCompany values
('Insurers', 'An insurance company headquartered at NYC', 'Insurance');


insert into CoopStudent values
(101, 'Hai H', 'hh@alumni.ubc.ca', 2, 'Computer Science');

insert into CoopStudent values
(102, 'Michael W', 'ms@eng.ubc.ca', 3, 'Engineering');

insert into CoopStudent values
(103, 'Ting Ting T', 'ttt@cs.ugrad.ubc.ca', 2, 'Computer Science');

insert into CoopStudent values
(104, 'Kevin T', 'kt@alumni.ubc.ca', 4, 'Computer Science');

insert into CoopStudent values
(105, 'John S', 'js@cs.ubc.ca', 1, 'Computer Science');

insert into CoopStudent values
(106, 'Jane D', 'jd@math.ubc.ca', 3, 'Math');

insert into CoopStudent values
(107, 'Susan D', 'sd@eng.ubc.ca', 2, 'English');


insert into PositionForCompany values
('Software Dev Co-op', 'HootSuite', 'To be cool, to develop software, to check for bugs', 'Vancouver', 'BC');

insert into PositionForCompany values
('Tech Co-op', 'TheFamousApp', 'To work on our famous app', 'Victoria', 'BC');

insert into PositionForCompany values
('Java Web Dev Co-op', 'The Web Devers', 'To develop java web apps', 'Silicon Valley', 'San Francisco');

insert into PositionForCompany values
('Co-op Student', 'The Inputters', 'To enter data into a given system', 'Silicon Valley', 'San Francisco');

insert into PositionForCompany values
('QA Co-op', 'HootSuite', 'To make test cases, do black box testing, etc.', 'Vancouver', 'BC');


insert into Review values
(1, 'Awesome company. Lots of good food and socials.', '2015-03-05', 'HootSuite', 101, 'Software Dev Co-op', 5);

insert into Review values
(2, 'Lamest company ever. Do not apply.', '2015-03-08', 'TheFamousApp', 102, 'Tech Co-op', 1);

insert into Review values
(3, 'Lots of challenging work in C and C++. Make sure you know your pointers', '2015-05-10', 'Microsoft', 103, 'Software Dev Co-op', 5);

insert into Review values
(4, 'If you are interested in web dev, this is the company to be at.', '2015-05-12', 'The Web Devers', 101, 'Java Web Dev Co-op', 4);

insert into Review values
(5, 'Rather repetitive work, but it is interesting!', '2015-04-11', 'The Inputters', 104, 'Co-op Student', 3);

insert into Review values
(6, 'Super company.', '2015-06-02', 'HootSuite', 105, 'QA Co-op', 4);


insert into PositionRequiresSkill values
('Software Dev Co-op', 'HootSuite', 'C++');

insert into PositionRequiresSkill values
('Software Dev Co-op', 'HootSuite', 'Java');

insert into PositionRequiresSkill values
('Tech Co-op', 'TheFamousApp', 'Python');

insert into PositionRequiresSkill values
('Software Dev Co-op', 'Microsoft', 'Java');

insert into PositionRequiresSkill values
('Java Web Dev Co-op', 'The Web Devers', 'C++');

insert into PositionRequiresSkill values
('Java Web Dev Co-op', 'The Web Devers', 'Python');

insert into PositionRequiresSkill values
('Co-op Student', 'The Inputters', 'Time management');


insert into CompanyHiresForDept values
('HootSuite', 'Engineering');

insert into CompanyHiresForDept values
('TheFamousApp', 'Computer Science');

insert into CompanyHiresForDept values
('Microsoft', 'Computer Science');

insert into CompanyHiresForDept values
('Microsoft', 'Engineering');

insert into CompanyHiresForDept values
('The Web Devers', 'Computer Science');

insert into CompanyHiresForDept values
('The Inputters', 'Computer Science');

insert into CompanyHiresForDept values
('The Chicken Farmers', 'Computer Science');

insert into CompanyHiresForDept values
('Insurers', 'Statistics');

insert into CompanyHiresForDept values
('Insurers', 'Math');


insert into CompanyLocation values
('HootSuite', 'Vancouver', 'BC');

insert into CompanyLocation values
('TheFamousApp', 'Victoria', 'BC');

insert into CompanyLocation values
('Microsoft', 'Seattle', 'Washington');

insert into CompanyLocation values
('Microsoft', 'Vancouver', 'BC');

insert into CompanyLocation values
('The Web Devers', 'Silicon Valley', 'San Francisco');

insert into CompanyLocation values
('The Inputters', 'Silicon Valley', 'San Francisco');

insert into CompanyLocation values
('The Chicken Farmers', 'Seattle', 'Washington');

insert into CompanyLocation values
('Insurers', 'Vancouver', 'BC');