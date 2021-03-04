describe('Login Admin Trello', () => {

    it('Login Amministratore Unico', () => {
        cy.visit('/')
        cy.get('input[name=email]').type('alessiopiccioli@webmapp.it')
        cy.get('input[name=password]').type('webmapp2020')
        cy.get('button').contains('Login').click()
        cy.url().should('contain', '/')
        cy.get('#nova > div > div.min-h-screen.flex-none.pt-header.min-h-screen.w-sidebar.bg-grad-sidebar.px-6 > ul > li:nth-child(1) > a\n')
            .should('contain', 'Customers')
        cy.get('#nova > div > div.min-h-screen.flex-none.pt-header.min-h-screen.w-sidebar.bg-grad-sidebar.px-6 > ul > li:nth-child(2) > a\n')
            .should('contain', 'Scrums')
        cy.get('#nova > div > div.min-h-screen.flex-none.pt-header.min-h-screen.w-sidebar.bg-grad-sidebar.px-6 > ul > li:nth-child(3) > a\n')
            .should('contain', 'Trello Cards')
        cy.get('#nova > div > div.min-h-screen.flex-none.pt-header.min-h-screen.w-sidebar.bg-grad-sidebar.px-6 > ul > li:nth-child(4) > a\n')
            .should('contain', 'Users')
        cy.get('#nova > div > div.min-h-screen.flex-none.pt-header.min-h-screen.w-sidebar.bg-grad-sidebar.px-6 > ul > li:nth-child(5) > a\n')
            .should('not.exist')

        cy.get('span.text-90').click()
        cy.contains('Logout').click()

    })




})
