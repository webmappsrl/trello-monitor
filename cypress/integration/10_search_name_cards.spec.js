describe('search Cards', () => {

    it('search Cards', () => {
        cy.visit('/nova/login')
        cy.get('input[name=email]').type('alessiopiccioli@webmapp.it')
        cy.get('input[name=password]').type('webmapp2020')
        cy.get('button').contains('Login').click()
        cy.url().should('contain', '/')

        cy.contains('Trello Card').click()
        cy.wait(1000)
        
        cy.get('#nova > div > div > div> div > div > div > input').eq(1).type('Come Alessio')

        cy.get('#nova > div > div > div > div > div > div > div > table > tbody > tr:nth-child(1) > td:nth-child(2) > div > span').should('exist')


        cy.get('span.text-90').click()
        cy.contains('Logout').click()



    })




})
