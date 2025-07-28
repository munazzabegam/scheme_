-- admin table
CREATE TABLE Admins (
    AdminID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Email VARCHAR(255) UNIQUE NOT NULL,
    PasswordHash VARCHAR(255) NOT NULL,
    Role ENUM('SuperAdmin', 'Verifier', 'Editor') DEFAULT 'Verifier',
    Status ENUM('Active', 'Inactive') DEFAULT 'Active',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE AdminLoginActivity (
    LoginID INT AUTO_INCREMENT PRIMARY KEY,
    AdminID INT NOT NULL,
    LoginTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    IPAddress VARCHAR(45),
    UserAgent TEXT,
    LoginStatus ENUM('Success', 'Failed') DEFAULT 'Success',
    FOREIGN KEY (AdminID) REFERENCES Admins(AdminID) ON DELETE CASCADE
);

CREATE TABLE AdminRememberTokens (
    TokenID INT AUTO_INCREMENT PRIMARY KEY,
    AdminID INT NOT NULL,
    Selector VARCHAR(32) NOT NULL UNIQUE,
    HashedValidator VARCHAR(255) NOT NULL,
    ExpiresAt DATETIME NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UserAgent TEXT,
    IPAddress VARCHAR(45),
    FOREIGN KEY (AdminID) REFERENCES Admins(AdminID) ON DELETE CASCADE
);


-- customers table
CREATE TABLE Customers (
    CustomerID INT AUTO_INCREMENT PRIMARY KEY,
    FullName VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NULL,
    PhoneNumber VARCHAR(15) NOT NULL,
    Address TEXT,
    DOB DATE,
    Gender ENUM('Male', 'Female', 'Other'),
    Status ENUM('Active', 'Inactive') DEFAULT 'Active',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE CustomerLoginActivity (
    LoginID INT AUTO_INCREMENT PRIMARY KEY,
    CustomerID INT NOT NULL,
    LoginTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    IPAddress VARCHAR(45),
    UserAgent TEXT,
    LoginStatus ENUM('Success', 'Failed') DEFAULT 'Success',
    FOREIGN KEY (CustomerID) REFERENCES Customers(CustomerID) ON DELETE CASCADE
);

-- payment
CREATE TABLE Payments (
    PaymentID INT AUTO_INCREMENT PRIMARY KEY,
    CustomerID INT NOT NULL,
    Amount DECIMAL(10,2) NOT NULL,
    PaymentDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PaymentMethod ENUM('UPI', 'Card', 'NetBanking', 'Cash', 'Wallet') NOT NULL,
    PaymentStatus ENUM('Pending', 'Success', 'Failed', 'Refunded') DEFAULT 'Pending',
    TransactionID VARCHAR(100),
    Notes TEXT,
    FOREIGN KEY (CustomerID) REFERENCES Customers(CustomerID) ON DELETE CASCADE
);

CREATE TABLE PaymentReceipts (
    ReceiptID INT AUTO_INCREMENT PRIMARY KEY,
    PaymentID INT NOT NULL,
    ReceiptNumber VARCHAR(50) UNIQUE,
    IssuedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    IssuedBy INT,
    FOREIGN KEY (PaymentID) REFERENCES Payments(PaymentID) ON DELETE CASCADE,
    FOREIGN KEY (IssuedBy) REFERENCES Admins(AdminID) ON DELETE SET NULL
);

CREATE TABLE Schemes (
    SchemeID INT AUTO_INCREMENT PRIMARY KEY,
    SchemeName VARCHAR(255) NOT NULL,
    SchemeImageURL VARCHAR(255),
    Description TEXT,
    MonthlyPayment DECIMAL(10,2) NOT NULL,
    TotalPayments INT NOT NULL,
    StartDate DATE,
    Status ENUM('Active', 'Closed', 'Upcoming') DEFAULT 'Active',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Scheme Enrollment
CREATE TABLE CustomerSchemeEnrollments (
    EnrollmentID INT AUTO_INCREMENT PRIMARY KEY,
    CustomerID INT NOT NULL,
    SchemeID INT NOT NULL,
    EnrolledOn DATE DEFAULT CURRENT_DATE,
    Status ENUM('Enrolled', 'Completed', 'Dropped') DEFAULT 'Enrolled',
    FOREIGN KEY (CustomerID) REFERENCES Customers(CustomerID) ON DELETE CASCADE,
    FOREIGN KEY (SchemeID) REFERENCES Schemes(SchemeID) ON DELETE CASCADE
);

CREATE TABLE Installments (
    InstallmentID INT AUTO_INCREMENT PRIMARY KEY,
    SchemeID INT NOT NULL,
    InstallmentName VARCHAR(255),
    InstallmentNumber INT NOT NULL,
    Amount DECIMAL(10,2) NOT NULL,
    DrawDate DATE,
    Benefits TEXT,
    ImageURL VARCHAR(255),
    Status ENUM('Pending', 'Paid', 'Overdue', 'Drawn') DEFAULT 'Pending',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (SchemeID) REFERENCES Schemes(SchemeID) ON DELETE CASCADE
);
-- notification
CREATE TABLE Notifications (
    NotificationID INT AUTO_INCREMENT PRIMARY KEY,
    ReceiverType ENUM('Admin', 'Customer') NOT NULL,
    ReceiverID INT NOT NULL,
    Title VARCHAR(255),
    Message TEXT NOT NULL,
    Link VARCHAR(255),
    IsRead BOOLEAN DEFAULT FALSE,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- whatsapp config
CREATE TABLE APIConfigurations (
    ConfigID INT AUTO_INCREMENT PRIMARY KEY,
    APIProviderName VARCHAR(100) NOT NULL,
    APIEndpoint VARCHAR(255) NOT NULL,
    AccessToken TEXT,
    Token TEXT,
    InstanceID VARCHAR(100),
    Status ENUM('Active', 'Inactive') DEFAULT 'Active',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- winners table
CREATE TABLE Winners (
    WinnerID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    UserType ENUM('Customer', 'Admin') NOT NULL,
    PrizeType VARCHAR(100) NOT NULL,
    WinningDate DATE NOT NULL,
    Status ENUM('Pending', 'Verified', 'Delivered', 'Rejected') DEFAULT 'Pending',
    AdminID INT,  -- Admin who verified or processed
    SchemeID INT,
    InstallmentID INT,
    DeliveryAddress TEXT,
    PreferredDeliveryDate DATE,
    Remarks TEXT,
    VerifiedAt TIMESTAMP NULL,

    FOREIGN KEY (AdminID) REFERENCES Admins(AdminID) ON DELETE SET NULL,
    FOREIGN KEY (SchemeID) REFERENCES Schemes(SchemeID) ON DELETE SET NULL,
    FOREIGN KEY (InstallmentID) REFERENCES Installments(InstallmentID) ON DELETE SET NULL
);

-- payment qr
CREATE TABLE PaymentQR (
    QRID INT AUTO_INCREMENT PRIMARY KEY,
    ProviderName VARCHAR(100),         -- e.g., UPI, PhonePe, Paytm
    QRCodeImagePath VARCHAR(255),      -- path to stored QR image (or use base64)
    UPIID VARCHAR(100),                -- optional: show UPI ID below the QR
    Status ENUM('Active', 'Inactive') DEFAULT 'Active',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- backup
CREATE TABLE backup (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    FileURL VARCHAR(255) NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE Customers DROP COLUMN AadharNumber;










