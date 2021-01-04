describe('Field readonly', () => {

    it('Field readonly', () => {
        cy.visit('/nova/login')
        cy.get('input[name=email]').type('gianmarcogagliardi@webmapp.it')
        cy.get('input[name=password]').type('webmapp2020')
        cy.get('button').contains('Login').click()
        cy.url().should('contain', '/')
        cy.contains('Trello Card').click()
        cy.url().should('contain', '/resources/trello-cards')

        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr:nth-child(1) > td:nth-child(5) > div > span > span>a').should('not.exist')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr:nth-child(1) > td:nth-child(4) > div > span > span>a').should('not.exist')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr:nth-child(1) > td:nth-child(3) > div > span > span>a').should('not.exist')


            cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > thead > tr > th:nth-child(6) > span\n').invoke('text').should((text3) => {
                expect('link').to.not.eq(text3)
            })

        cy.get('span.text-90').click()
        cy.contains('Logout').click()



    })




})
