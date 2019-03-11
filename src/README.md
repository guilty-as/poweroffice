# Poweroffice

Poweroffice is an application used for billing, invoicing etc.


## How the Invoice integration works.

The CreatePowerOfficeInvoice command is scheduled to run every minute


1. It creates the paying customer as a customer in PowerOffice.
2. If the course/product does not exist in poweroffice, it will be created,.
3. The invoice will be generated with the correct invoice line items.
