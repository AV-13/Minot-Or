#------------------------------------------------------------
# MySQL Script (Translated to English)
#------------------------------------------------------------

#------------------------------------------------------------
# Table: Company
#------------------------------------------------------------
CREATE TABLE Company(
        idCompany         INT AUTO_INCREMENT NOT NULL,
        companyName       VARCHAR(50) NOT NULL,
        companySiret      VARCHAR(50) NOT NULL,
        companyContact    VARCHAR(50) NOT NULL,
    CONSTRAINT Company_PK PRIMARY KEY (idCompany)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: User
#------------------------------------------------------------
CREATE TABLE User(
        idUser             INT AUTO_INCREMENT NOT NULL,
        lastName           VARCHAR(50) NOT NULL,
        firstName          VARCHAR(50) NOT NULL,
        password           VARCHAR(50) NOT NULL,
        role               ENUM('Baker','Sales','Driver','OrderPreparer','Miller','Procurement') NOT NULL,
        email              VARCHAR(50) NOT NULL,
        idCompany          INT NOT NULL,
    CONSTRAINT User_Email_IDX INDEX (email),
    CONSTRAINT User_PK PRIMARY KEY (idUser),
    CONSTRAINT User_Company_FK FOREIGN KEY (idCompany) REFERENCES Company(idCompany)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Warehouse
#------------------------------------------------------------
CREATE TABLE Warehouse(
        idWarehouse       INT AUTO_INCREMENT NOT NULL,
        warehouseAddress  VARCHAR(10) NOT NULL,
        storageCapacity   INT NOT NULL,
    CONSTRAINT Warehouse_PK PRIMARY KEY (idWarehouse)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Product
#------------------------------------------------------------
CREATE TABLE Product(
        idProduct               INT AUTO_INCREMENT NOT NULL,
        productName             VARCHAR(50) NOT NULL,
        quantity                FLOAT NOT NULL,
        netPrice                FLOAT NOT NULL,
        grossPrice              FLOAT NOT NULL,
        unitWeight              FLOAT NOT NULL,
        category                ENUM('flour','oil','egg','yeast','salt','sugar','butter','milk','seed','chocolate','bread') NOT NULL,
        stockQuantity           INT NOT NULL,
        idWarehouse             INT NOT NULL,
    CONSTRAINT Product_PK PRIMARY KEY (idProduct),
    CONSTRAINT Product_Warehouse_FK FOREIGN KEY (idWarehouse) REFERENCES Warehouse(idWarehouse)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Supplier
#------------------------------------------------------------
CREATE TABLE Supplier(
        idSupplier        INT AUTO_INCREMENT NOT NULL,
        supplierName      VARCHAR(50) NOT NULL,
        supplierAddress   VARCHAR(50) NOT NULL,
    CONSTRAINT Supplier_PK PRIMARY KEY (idSupplier)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: TruckCleaning
#------------------------------------------------------------
CREATE TABLE TruckCleaning(
        idTruckCleaning     INT AUTO_INCREMENT NOT NULL,
        cleaningStartDate   DATE NOT NULL,
        cleaningEndDate     DATE NOT NULL,
        observations        TEXT NOT NULL,
    CONSTRAINT TruckCleaning_PK PRIMARY KEY (idTruckCleaning)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Pricing
#------------------------------------------------------------
CREATE TABLE Pricing(
        idPricing        INT AUTO_INCREMENT NOT NULL,
        fixedFee         FLOAT NOT NULL,
        modificationDate DATETIME NOT NULL,
        costPerKm        FLOAT NOT NULL,
    CONSTRAINT Pricing_PK PRIMARY KEY (idPricing)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: SalesList
#------------------------------------------------------------
CREATE TABLE SalesList(
        idSalesList         INT AUTO_INCREMENT NOT NULL,
        status              ENUM('pending','preparing_products','awaiting_delivery') NOT NULL,
        productsPrice       FLOAT NOT NULL,
        globalDiscount      INT NOT NULL,
        issueDate           DATETIME NOT NULL,
        expirationDate      DATETIME NOT NULL,
        orderDate           DATETIME NOT NULL,
    CONSTRAINT SalesList_PK PRIMARY KEY (idSalesList)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Delivery
#------------------------------------------------------------
CREATE TABLE Delivery(
        idDelivery           INT AUTO_INCREMENT NOT NULL,
        deliveryDate         DATE NOT NULL,
        deliveryAddress      VARCHAR(10) NOT NULL,
        deliveryNumber       VARCHAR(10) NOT NULL,
        deliveryStatus       ENUM('in_preparation','in_progress','delivered') NOT NULL,
        driverRemark         CHAR(5) NOT NULL,
        qrCode               TEXT NOT NULL,
        idSalesList          INT NOT NULL,
    CONSTRAINT Delivery_PK PRIMARY KEY (idDelivery),
    CONSTRAINT Delivery_SalesList_FK FOREIGN KEY (idSalesList) REFERENCES SalesList(idSalesList),
    CONSTRAINT Delivery_SalesList_AK UNIQUE (idSalesList)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Truck
#------------------------------------------------------------
CREATE TABLE Truck(
        idTruck               INT AUTO_INCREMENT NOT NULL,
        registrationNumber    VARCHAR(10) NOT NULL,
        truckType             VARCHAR(50) NOT NULL,
        isAvailable           BOOL NOT NULL,
        lastCleaning          DATE NOT NULL,
        deliveryCount         INT NOT NULL,
        transportDistance     FLOAT NOT NULL,
        transportFee          FLOAT NOT NULL,
        idDelivery            INT,
        idWarehouse           INT NOT NULL,
        idUser                INT,
    CONSTRAINT Truck_PK PRIMARY KEY (idTruck),
    CONSTRAINT Truck_Delivery_FK FOREIGN KEY (idDelivery) REFERENCES Delivery(idDelivery),
    CONSTRAINT Truck_Warehouse_FK FOREIGN KEY (idWarehouse) REFERENCES Warehouse(idWarehouse),
    CONSTRAINT Truck_User_FK FOREIGN KEY (idUser) REFERENCES User(idUser)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Invoice
#------------------------------------------------------------
CREATE TABLE Invoice(
        idInvoice           INT AUTO_INCREMENT NOT NULL,
        totalAmount         FLOAT NOT NULL,
        issueDate           DATE NOT NULL,
        dueDate             DATE NOT NULL,
        paymentStatus       BOOL NOT NULL,
        acceptanceDate      DATE NOT NULL,
        idSalesList         INT NOT NULL,
        idPricing           INT NOT NULL,
    CONSTRAINT Invoice_PK PRIMARY KEY (idInvoice),
    CONSTRAINT Invoice_SalesList_FK FOREIGN KEY (idSalesList) REFERENCES SalesList(idSalesList),
    CONSTRAINT Invoice_Pricing_FK FOREIGN KEY (idPricing) REFERENCES Pricing(idPricing),
    CONSTRAINT Invoice_SalesList_AK UNIQUE (idSalesList)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Contains
#------------------------------------------------------------
CREATE TABLE Contains(
        idSalesList        INT NOT NULL,
        idProduct          INT NOT NULL,
        productQuantity    INT NOT NULL,
        productDiscount    INT NOT NULL,
    CONSTRAINT Contains_PK PRIMARY KEY (idSalesList,idProduct),
    CONSTRAINT Contains_SalesList_FK FOREIGN KEY (idSalesList) REFERENCES SalesList(idSalesList),
    CONSTRAINT Contains_Product_FK FOREIGN KEY (idProduct) REFERENCES Product(idProduct)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Evaluate
#------------------------------------------------------------
CREATE TABLE Evaluate(
        idSalesList     INT NOT NULL,
        idUser          INT NOT NULL,
        quoteAccepted   BOOL NOT NULL,
    CONSTRAINT Evaluate_PK PRIMARY KEY (idSalesList,idUser),
    CONSTRAINT Evaluate_SalesList_FK FOREIGN KEY (idSalesList) REFERENCES SalesList(idSalesList),
    CONSTRAINT Evaluate_User_FK FOREIGN KEY (idUser) REFERENCES User(idUser)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: ProductSupplier
#------------------------------------------------------------
CREATE TABLE ProductSupplier(
        idProduct       INT NOT NULL,
        idSupplier      INT NOT NULL,
    CONSTRAINT ProductSupplier_PK PRIMARY KEY (idProduct,idSupplier),
    CONSTRAINT ProductSupplier_Product_FK FOREIGN KEY (idProduct) REFERENCES Product(idProduct),
    CONSTRAINT ProductSupplier_Supplier_FK FOREIGN KEY (idSupplier) REFERENCES Supplier(idSupplier)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Restock
#------------------------------------------------------------
CREATE TABLE Restock(
        idSupplier                  INT NOT NULL,
        idTruck                     INT NOT NULL,
        idProduct                   INT NOT NULL,
        supplierProductQuantity     INT NOT NULL,
        orderNumber                 VARCHAR(10) NOT NULL,
        orderDate                   DATE NOT NULL,
        orderStatus                 ENUM('pending','preparing_products','awaiting_delivery') NOT NULL,
    CONSTRAINT Restock_PK PRIMARY KEY (idSupplier,idTruck,idProduct),
    CONSTRAINT Restock_Supplier_FK FOREIGN KEY (idSupplier) REFERENCES Supplier(idSupplier),
    CONSTRAINT Restock_Truck_FK FOREIGN KEY (idTruck) REFERENCES Truck(idTruck),
    CONSTRAINT Restock_Product_FK FOREIGN KEY (idProduct) REFERENCES Product(idProduct)
) ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Clean
#------------------------------------------------------------
CREATE TABLE Clean(
        idTruckCleaning   INT NOT NULL,
        idTruck           INT NOT NULL,
    CONSTRAINT Clean_PK PRIMARY KEY (idTruckCleaning,idTruck),
    CONSTRAINT Clean_TruckCleaning_FK FOREIGN KEY (idTruckCleaning) REFERENCES TruckCleaning(idTruckCleaning),
    CONSTRAINT Clean_Truck_FK FOREIGN KEY (idTruck) REFERENCES Truck(idTruck)
) ENGINE=InnoDB;
