/*
Milestone 4 SQL Script

Haiden Murphy
Jauseff Dait
Nick Nguyen
Khushi

This script contains all tables for the Rock Climbing Gym Database. Each table is populated with five tuples of sample data.
*/

CREATE TABLE Gym (
    gymId INT PRIMARY KEY,
    gymAddress VARCHAR(255) NOT NULL,
    PostalCode VARCHAR(20),
    owner VARCHAR(50) NOT NULL,
    NumOfMembers INT DEFAULT 0
);

CREATE TABLE ClimbingWalls (
    wID INT PRIMARY KEY,
    gymID INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    prevResetDate DATE,
    climbingStyle VARCHAR(50),
    FOREIGN KEY (gymID) REFERENCES Gym(gymId) ON DELETE CASCADE
);

CREATE TABLE Climber (
    climberId INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    sex CHAR(1),
    dob DATE
);

CREATE TABLE AgeGroup (
    dob DATE PRIMARY KEY,
    ageGroup VARCHAR(20)
);

CREATE TABLE Classes (
    Class_ID INT PRIMARY KEY,
    Event_Date DATE NOT NULL,
    Class_Name VARCHAR(100) NOT NULL
);

CREATE TABLE taughtOn (
    class_Id INT,
    wallId INT,
    PRIMARY KEY(class_Id, wallId),
    FOREIGN KEY(class_Id) REFERENCES Classes(Class_ID) ON DELETE CASCADE,
    FOREIGN KEY(wallId) REFERENCES ClimbingWalls(wID) ON DELETE CASCADE
);

CREATE TABLE Equipment (
    eID INT PRIMARY KEY,
    gymId INT NOT NULL,
    eName VARCHAR(100),
    section VARCHAR(50),
    nextMaintenanceDate DATE,
    FOREIGN KEY(gymId) REFERENCES Gym(gymId) ON DELETE CASCADE
);

CREATE TABLE Rents (
    eID INT,
    climberId INT,
    rent_time DATETIME NOT NULL,
    rent_date DATE NOT NULL,
    duration INT,
    PRIMARY KEY (eID, climberId, rent_time, rent_date),
    FOREIGN KEY (eID) REFERENCES Equipment(eID),
    FOREIGN KEY (climberId) REFERENCES Climber(climberId)
);

CREATE TABLE Teaches (
    classId INT,
    climberId INT NOT NULL,
    role VARCHAR(50) NOT NULL,
    PRIMARY KEY(classId, climberId),
    FOREIGN KEY(classId) REFERENCES Classes(Class_ID) ON DELETE CASCADE,
    FOREIGN KEY(climberId) REFERENCES Climber(climberId) ON DELETE CASCADE
);

CREATE TABLE Uses (
    wID INT,
    climberId INT,
    time DATE,
    gymId INT,
    duration INT,
    PRIMARY KEY(climberId, wID, time),
    FOREIGN KEY(climberId) REFERENCES Climber(climberId) ON DELETE CASCADE,
    FOREIGN KEY(wID) REFERENCES ClimbingWalls(wID) ON DELETE CASCADE,
    FOREIGN KEY(gymId) REFERENCES Gym(gymId) ON DELETE CASCADE
);

CREATE TABLE SectionMaintenance (
    section VARCHAR(50) PRIMARY KEY,
    nextMaintenanceDate DATE
);

CREATE TABLE Members (
    climberId INT PRIMARY KEY,
    dateJoined DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY(climberId) REFERENCES Climber(climberId) ON DELETE CASCADE
);

CREATE TABLE Staff (
    climberId INT PRIMARY KEY,
    YearsOfExperience INT DEFAULT 0,
    FOREIGN KEY(climberId) REFERENCES Climber(climberId) ON DELETE CASCADE
);

CREATE TABLE Supervises (
    supervisorId INT,
    subordinateId INT PRIMARY KEY,
    FOREIGN KEY(supervisorId) REFERENCES Climber(climberId) ON DELETE SET NULL,
    FOREIGN KEY(subordinateId) REFERENCES Climber(climberId) ON DELETE CASCADE
);

CREATE TABLE Prerequisite (
    beginners_classId INT,
    highLevel_classId INT,
    PRIMARY KEY(beginners_classId, highLevel_classId),
    FOREIGN KEY(beginners_classId) REFERENCES Classes(Class_ID) ON DELETE CASCADE,
    FOREIGN KEY(highLevel_classId) REFERENCES Classes(Class_ID) ON DELETE CASCADE
);

CREATE TABLE Waitlist (
    QueueNum INT,
    classId INT,
    PRIMARY KEY(QueueNum, classId),
    FOREIGN KEY(classId) REFERENCES Classes(Class_ID) ON DELETE CASCADE
);

CREATE TABLE Waitlist_IsOn (
    QueueNum INT,
    classId INT,
    climberId INT UNIQUE,
    PRIMARY KEY(QueueNum, classId),
    FOREIGN KEY(QueueNum, classId) REFERENCES Waitlist(QueueNum, classId) ON DELETE CASCADE,
    FOREIGN KEY(climberId) REFERENCES Climber(climberId) ON DELETE CASCADE
);

-- INSERT STATEMENTS ----------------------------------------------------------------------------

INSERT INTO Gym VALUES
(1, '123 Main St, Vancouver', 'V5K0A1', 'Alice Smith', 200),
(2, '456 Oak Ave, Vancouver', 'V6B1C2', 'Bob Johnson', 150),
(3, '789 Pine Rd, Burnaby', 'V5H2Z3', 'Charlie Lee', 180),
(4, '321 Maple St, Richmond', 'V6Y3X4', 'Diana King', 220),
(5, '654 Cedar Blvd, Surrey', 'V3T4W5', 'Ethan Brown', 170);

INSERT INTO ClimbingWalls VALUES
(1, 1, 'The Boulder Cave', '2026-01-15', 'Bouldering'),
(2, 1, 'Skyline Traverse', '2026-02-01', 'Top Rope'),
(3, 2, 'Overhang Challenge', '2026-01-20', 'Lead Climbing'),
(4, 3, 'Slab Zone', '2026-01-25', 'Slab Climbing'),
(5, 4, 'Speed Alley', '2026-02-05', 'Speed Climbing');

INSERT INTO Climber VALUES
(101, 'Emma White', 'F', '2000-05-12'),
(102, 'Liam', 'M', '1998-11-03'),
(103, 'Olivia Black', 'F', '2001-02-18'),
(104, 'Noah J', 'M', '1999-07-22'),
(105, 'Ava Grey', 'F', '2002-09-10');

INSERT INTO AgeGroup VALUES
('2012-06-15', 'Child'),
('2008-09-22', 'Teen'),
('2000-05-12', 'Adult'),
('1965-11-03', 'Senior'),
('2005-02-18', 'Teen');

INSERT INTO Classes VALUES
(201, '2026-03-15', 'Beginner Bouldering'),
(202, '2026-03-16', 'Lead Climbing 101'),
(203, '2026-03-17', 'Top Rope Techniques'),
(204, '2026-03-18', 'Intermediate Bouldering'),
(205, '2026-03-19', 'Advanced Lead Climbing');

INSERT INTO taughtOn VALUES
(201, 1),
(202, 2),
(203, 3),
(204, 4),
(205, 5);

INSERT INTO Equipment VALUES
(301, 1, 'Climbing Rope', 'Ropes', '2026-04-01'),
(302, 1, 'Harness', 'Gear', '2026-04-10'),
(303, 2, 'Carabiner Set', 'Gear', '2026-04-15'),
(304, 3, 'Crash Pad', 'Pads', '2026-04-20'),
(305, 4, 'Belay Device', 'Gear', '2026-04-25');

INSERT INTO Rents VALUES
(301, 101, '2026-03-01 10:00:00', '2026-03-01', 2),
(302, 102, '2026-03-02 11:00:00', '2026-03-02', 3),
(303, 103, '2026-03-03 12:00:00', '2026-03-03', 1),
(304, 104, '2026-03-04 13:00:00', '2026-03-04', 2),
(305, 105, '2026-03-05 14:00:00', '2026-03-05', 3);

INSERT INTO Teaches VALUES
(201, 101, 'Lead Instructor'),
(202, 102, 'Assistant Instructor'),
(203, 103, 'Climbing Coach'),
(204, 104, 'Safety Officer'),
(205, 105, 'Program Coordinator');

INSERT INTO Uses VALUES
(1, 101, '2026-03-01', 1, 2),
(2, 102, '2026-03-02', 1, 3),
(3, 103, '2026-03-03', 2, 1),
(4, 104, '2026-03-04', 3, 2),
(5, 105, '2026-03-05', 4, 3);

INSERT INTO SectionMaintenance VALUES
('Ropes', '2026-04-01'),
('Gear', '2026-04-15'),
('Pads', '2026-04-20'),
('Walls', '2026-05-01'),
('Shoes', '2026-04-30');

INSERT INTO Members VALUES
(101, '2025-01-10'),
(102, '2025-02-15'),
(103, '2025-03-20'),
(104, '2025-04-25'),
(105, '2025-05-30');
