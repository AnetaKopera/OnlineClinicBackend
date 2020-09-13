create database OnlineClinic; 

use OnlineClinic;

create table Users
(
    id int(5) auto_increment primary key,
	surname varchar(50) not null,
	name varchar(50) not null,
	email varchar(50) not null,
	password varchar(50) not null, 
	accountType varchar(50) not null
);

create table Clinics
(
	id int(5) auto_increment primary key,
	nameOfClinic varchar(50) not null,
	city varchar(30) not null,
	street varchar(50) not null,
	email varchar(50) not null,
	telephone varchar(50) not null
);

create table TimeOfWorking
(
	id int(5)  auto_increment primary key,
	mondayStart time(0),
	mondayStop time(0),
	tuesdayStart time(0),
	tuesdayStop time(0),
	wednesdayStart time(0),
	wednesdayStop time(0),
	thursdayStart time(0),
	thursdayStop time(0),
	fridayStart time(0),
	fridayStop time(0),
	saturdayStart time(0),
	saturdayStop time(0),
	sundayStart time(0),
	sundayStop time(0)
);


create table Services
(
	id int(5)  auto_increment primary key,
	typeOfService varchar(20) not null,
	description varchar(500),
	price int(20) not null,
	timeOfService int(20) not null,
	idClinic int(5) not null,
    foreign key (idClinic) references Clinics(id)
);

create table Doctors
(
	id int(5) auto_increment primary key,
	idUser int(5) not null,
    foreign key (idUser) references Users(id),
	specialization varchar (50) not null,
	idClinic int(5) not null,
    foreign key (idClinic) references Clinics(id),
	idWorkSchedule int(5) not null,
    foreign key(idWorkSchedule) references TimeOfWorking(id)
	
);

create table Visits
(
	id int(5) auto_increment primary key,
	idService int(5) not null,
	dateVisit date not null,
	hourVisit time not null,
	payInAdvance char(1) not null,
	idDoctor int(5) not null,
	idUser int(5) not null,
	foreign key (idService) references Services(id),
    foreign key (idDoctor) references Doctors(id),
    foreign key (idUser) references Users(id)
);


create table Opinions
(
	id int(5) auto_increment primary key,
	idUser int(5) not null,
    foreign key (idUser) references Users(id),
	idClinic int(5) not null,
    foreign key (idClinic) references Clinics(id),
	idService int(5) not null,
    foreign key (idService) references Services(id),
	opinion varchar(500) not null,
	stars int(1) not null
	
);


create table Tenure
(
	id int(5) auto_increment primary key,
	idClinic int(5) not null,
    foreign key (idClinic) references Clinics(id),
	idService int(5) not null,
    foreign key (idService) references Services(id),
	idDoctor int(5) not null,
    foreign key (idDoctor) references Doctors(id)
	
);
