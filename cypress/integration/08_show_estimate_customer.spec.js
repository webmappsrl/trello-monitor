describe('Check Estimate Show', () => {

    it('Check Estimate Show', () => {
        cy.visit('/')
        cy.get('input[name=email]').type('alessiopiccioli@webmapp.it')
        cy.get('input[name=password]').type('webmapp2020')
        cy.get('button').contains('Login').click()
        cy.url().should('contain', '/')

        cy.contains('Trello Cards').click()

        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr:nth-child(1) > td:nth-child(7) > div ').each(($e, index, $list) => {
            cy.log($e.text())
            const estimateHome = $e.text()
            cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr:nth-child(1) > td:nth-child(2) > div ').click()
            cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr:nth-child(1) > td:nth-child(7) > div ')
                .each(($el, index, $list) => {
                expect(estimateHome).to.eq($el.text())
            })
        })
    })
})
