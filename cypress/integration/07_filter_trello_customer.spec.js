describe('Filter Customer', () => {

    it('Filter Customer', () => {
        cy.visit('/')
        cy.get('input[name=email]').type('gianmarcogagliardi@webmapp.it')
        cy.get('input[name=password]').type('webmapp2020')
        cy.get('button').contains('Login').click()
        cy.url().should('contain', '/')

        cy.contains('Trello Cards').click()
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.flex.items-center.py-3.border-b.border-50 > div.flex.items-center.ml-auto.px-3 > div > div > button > div').click()
        cy.get('select.block.w-full.form-control-sm.form-select').eq(1).select('CYCLANDO')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.flex.items-center.py-3.border-b.border-50 > div.flex.items-center.ml-auto.px-3 > div > div > button > div').click()

        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(6) > div > span').each(($e, index, $list) => {
            const text = $e.text()
            if(text == 'CYCLANDO')
                expect(text).to.eq('CYCLANDO')
            if (text == 'Cyclando')
                expect(text).to.eq('Cyclando')
        })



        cy.get('span.text-90').click()
        cy.contains('Logout').click()



    })




})
