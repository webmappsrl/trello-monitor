describe('Not create Card', () => {

    it('Not create Card', () => {
        cy.visit('/nova/login')
        cy.get('input[name=email]').type('alessiopiccioli@webmapp.it')
        cy.get('input[name=password]').type('webmapp2020')
        cy.get('button').contains('Login').click()
        cy.url().should('contain', '/')

        cy.contains('Trello Card').click()
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.flex.items-center.py-3.border-b.border-50 > div.flex.items-center.ml-auto.px-3 > div > div > button > div').click()
        cy.get('select.block.w-full.form-control-sm.form-select').eq(3).select('Davide Pizzato')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.flex.items-center.py-3.border-b.border-50 > div.flex.items-center.ml-auto.px-3 > div > div > button > div').click()
cy.wait(1000)
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(4) > div > span > span').each(($e, index, $list) => {
            const text = $e.text()

                expect(text).to.eq('Davide Pizzato')

        })



        cy.get('span.text-90').click()
        cy.contains('Logout').click()



    })




})
