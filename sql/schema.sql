DROP DATABASE IF EXISTS greeliving;
CREATE DATABASE greeliving;

DROP TABLE IF EXISTS greeliving.CourseApplicant;
DROP TABLE IF EXISTS greeliving.WorkExperience;
DROP TABLE IF EXISTS greeliving.ExtracurricularExperience;
DROP TABLE IF EXISTS greeliving.Skill;
DROP TABLE IF EXISTS greeliving.Applicant;
DROP TABLE IF EXISTS greeliving.Course;
DROP TABLE IF EXISTS greeliving.Job;
DROP TABLE IF EXISTS greeliving.Company;
DROP TABLE IF EXISTS greeliving.Specialization;
DROP TABLE IF EXISTS greeliving.InterviewType;
DROP TABLE IF EXISTS greeliving.InPersonInterviewDate;
DROP TABLE IF EXISTS greeliving.InPersonInterview;
DROP TABLE IF EXISTS greeliving.OnTheGoInterview;
DROP TABLE IF EXISTS greeliving.Interview;
DROP TABLE IF EXISTS greeliving.JobApplication;
DROP TABLE IF EXISTS greeliving.SavedJob;

CREATE TABLE greeliving.Applicant (
    ApplicantID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    AuthenticationID VARCHAR(100) NOT NULL,
    FirstName VARCHAR(100) NOT NULL,
    LastName VARCHAR(100) NOT NULL,
    Birthdate DATE NOT NULL,
    Gender ENUM("Male", "Female", "Non-binary", "Other") NOT NULL,
    Email VARCHAR(100) NOT NULL,
    Phone VARCHAR(10) NOT NULL,
    Nationality VARCHAR(100),
    CountryOfResidence VARCHAR(100),
    City VARCHAR(100),
    District VARCHAR(100),
    StreetAddress VARCHAR(100),
    JobTitle VARCHAR(100),
    ExperienceLevel ENUM("Internship", "Entry level", "Junior", "Mid-level", "Senior"),
    EducationBackground ENUM("Not graduated", "Intermediate degree", "High school degree", "College degree", "Undergraduate degree", "Postgraduate degree", "Other"),
    CareerGoal TEXT
);

CREATE TABLE greeliving.Course (
    CourseID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    CourseName VARCHAR(100) NOT NULL
);

CREATE TABLE greeliving.CourseApplicant (
    CourseID INT NOT NULL,
    ApplicantID INT NOT NULL,
    CourseStatus ENUM("In progress", "Completed") NOT NULL DEFAULT "In progress",
    
    CONSTRAINT PRIMARY KEY (CourseID, ApplicantID),

    CONSTRAINT FOREIGN KEY (CourseID)
        REFERENCES Course(CourseID)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT FOREIGN KEY (ApplicantID)
        REFERENCES Applicant(ApplicantID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE greeliving.WorkExperience (
    WorkExperienceID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    ApplicantID INT NOT NULL,
    Company VARCHAR(100) NOT NULL,
    JobTitle VARCHAR(100) NOT NULL,
    StartDate DATE NOT NULL,
    EndDate DATE,
    ScopeOfWork TEXT,

    CONSTRAINT FOREIGN KEY (ApplicantID)
        REFERENCES Applicant(ApplicantID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE greeliving.ExtracurricularExperience (
    ExtracurricularExperienceID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    ApplicantID INT NOT NULL,
    Organization VARCHAR(100) NOT NULL,
    JobTitle VARCHAR(100) NOT NULL,
    StartDate DATE NOT NULL,
    EndDate DATE,
    ScopeOfWork TEXT,

    CONSTRAINT FOREIGN KEY (ApplicantID)
        REFERENCES Applicant(ApplicantID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE greeliving.Skill (
    SkillID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    ApplicantID INT NOT NULL,
    SkillName VARCHAR(100) NOT NULL,
    Proficiency ENUM("Low", "Moderate", "High", "Very high") NOT NULL,
    SkillDescription TEXT,

    CONSTRAINT FOREIGN KEY (ApplicantID)
        REFERENCES Applicant(ApplicantID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE greeliving.Company (
    CompanyID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    AuthenticationID VARCHAR(100) NOT NULL,
    CompanyName VARCHAR(100) NOT NULL,
    CompanySize ENUM("1-20 employees", "21-50 employees", "51-100 employees", "100+ employees") NOT NULL,
    Phone VARCHAR(10) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    Introduction TEXT NOT NULL
);

CREATE TABLE greeliving.Specialization (
    SpecializationID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    SpecializationName VARCHAR(100) NOT NULL
);

CREATE TABLE greeliving.Job (
    JobID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    CompanyID INT NOT NULL,
    JobTitle VARCHAR(100) NOT NULL,
    DatePosted DATETIME NOT NULL DEFAULT NOW(),
    ApplicationDeadline DATETIME NOT NULL,
    Salary DECIMAL(10,2) NOT NULL,
    WorkingLocation VARCHAR(100) NOT NULL,
    SpecializationID INT NOT NULL,
    ExperienceRequirement ENUM("Internship", "Entry level", "Junior", "Mid-level", "Senior") NOT NULL,
    WorkingFormat ENUM("On-site", "Remote", "Hybrid") NOT NULL,
    ScopeOfWork TEXT NOT NULL,
    Benefits TEXT NOT NULL,

    CONSTRAINT FOREIGN KEY (CompanyID)
        REFERENCES Company(CompanyID)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT FOREIGN KEY (SpecializationID)
        REFERENCES Specialization(SpecializationID)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE greeliving.SavedJob (
	JobID INT NOT NULL,
    ApplicantID INT NOT NULL,
    
    CONSTRAINT PRIMARY KEY (JobID, ApplicantID),
    
    CONSTRAINT FOREIGN KEY (ApplicantID)
        REFERENCES Applicant(ApplicantID)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT FOREIGN KEY (JobID)
        REFERENCES Job(JobID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);    

CREATE TABLE greeliving.JobApplication (
    ApplicationID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    JobID INT NOT NULL,
    ApplicantID INT NOT NULL,
    TimeOfApplication DATETIME NOT NULL DEFAULT NOW(),
    CV VARCHAR(255) NOT NULL,
    StatementOfPurpose TEXT NOT NULL,
    ExpectToGain TEXT NOT NULL,
    Questions TEXT,
    ApplicationStatus ENUM("Applying", "Reviewing", "Failed", "Succeeded", "Interviewing") NOT NULL,

    CONSTRAINT FOREIGN KEY (ApplicantID)
        REFERENCES Applicant(ApplicantID)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT FOREIGN KEY (JobID)
        REFERENCES Job(JobID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE greeliving.InterviewType (
    InterviewTypeID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    InterviewType VARCHAR(10) NOT NULL
);

CREATE TABLE greeliving.Interview (
    ApplicationID INT NOT NULL,
    InterviewTypeID INT NOT NULL,
    
    CONSTRAINT PRIMARY KEY (ApplicationID),

    CONSTRAINT FOREIGN KEY (ApplicationID)
        REFERENCES JobApplication(ApplicationID)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT FOREIGN KEY (InterviewTypeID)
        REFERENCES InterviewType(InterviewTypeID)
        ON DELETE RESTRICT
        ON UPDATE RESTRICT,

    CONSTRAINT UC_Interview UNIQUE (ApplicationID, InterviewTypeID)
);

CREATE TABLE greeliving.InPersonInterview (
    ApplicationID INT NOT NULL PRIMARY KEY,
    InterviewTypeID INT GENERATED ALWAYS AS ('1') STORED,

    CONSTRAINT FOREIGN KEY (ApplicationID, InterviewTypeID)
        REFERENCES Interview(ApplicationID, InterviewTypeID)
        ON DELETE CASCADE
        ON UPDATE RESTRICT
);

CREATE TABLE greeliving.OnTheGoInterview (
    ApplicationID INT NOT NULL PRIMARY KEY,
    InterviewTypeID INT GENERATED ALWAYS AS ('2') STORED,
    InterviewTimeFrom DATETIME NOT NULL,
    InterviewTimeTo DATETIME NOT NULL,
    InterviewLink VARCHAR(255) NOT NULL,

    CONSTRAINT FOREIGN KEY (ApplicationID, InterviewTypeID)
        REFERENCES Interview(ApplicationID, InterviewTypeID)
        ON DELETE CASCADE
        ON UPDATE RESTRICT
);

CREATE TABLE greeliving.InPersonInterviewDate (
    ApplicationID INT NOT NULL,
    InterviewTimeFrom DATETIME NOT NULL,
    InterviewTimeTo DATETIME NOT NULL,
    Booked BOOLEAN NOT NULL DEFAULT '0',

    CONSTRAINT PRIMARY KEY (ApplicationID, InterviewTimeFrom, InterviewTimeTo),

    CONSTRAINT FOREIGN KEY (ApplicationID)
        REFERENCES InPersonInterview(ApplicationID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE INDEX i_salary ON greeliving.Job (Salary);

INSERT INTO greeliving.InterviewType (InterviewType) VALUES ("in-person"), ("on-the-go");

DELIMITER $$
CREATE TRIGGER greeliving.JobApplication_Deadline_Trigger 
	BEFORE INSERT ON greeliving.JobApplication
	FOR EACH ROW
BEGIN
	DECLARE Deadline DATETIME;
    
	SELECT ApplicationDeadline INTO Deadline FROM greeliving.Job WHERE JobID = new.JobID;
    
    IF new.TimeOfApplication > Deadline THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "Cannot apply after the application deadline!";
	END IF;
END $$

DELIMITER ;