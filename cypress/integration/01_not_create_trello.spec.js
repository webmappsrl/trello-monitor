describe('Not create Card', () => {

    it('Not create Card', () => {
        cy.visit('/')
        cy.get('input[name=email]').type('alessiopiccioli@webmapp.it')
        cy.get('input[name=password]').type('webmapp2020')
        cy.get('button').contains('Login').click()
        cy.url().should('contain', '/')

        cy.contains('Trello Cards').click()
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.flex > div.w-full.flex.items-center.mb-6 > div.flex-no-shrink.ml-auto > a').should('not.exist')

        cy.wait(1000)
        cy.get('span.text-90').click()
        cy.contains('Logout').click()
    })
})
