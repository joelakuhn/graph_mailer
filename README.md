# Graph Mailer

Send emails from WordPress through Microsoft's dumbass Graph API.

## How to Use

Log into Azure and go to "App registrations". Add a "New registration", and include the callback URL from the plugin admin page in the Redirect URI field on the application registration page (this can also be done later).

From Azure you will need:

- Application (client) ID
- Directory (tenant) ID
- Client Secret

Application ID and Tenant ID can be found on the main page of your Azure app registration. The client secret must be generated under "Client credentials".

Once you have these three pieces in the app, you can click "Authorize Now" and follow the authentication flow. If the application flow succeeds, you will be back on the plugin admin page with a success message. You are now ready to send email through Microsoft Graph API.