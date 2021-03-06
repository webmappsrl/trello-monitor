describe('Estimate', () => {

    it('Estimate', () => {
        cy.visit('/')
        cy.get('input[name=email]').type('alessiopiccioli@webmapp.it')
        cy.get('input[name=password]').type('webmapp2020')
        cy.get('button').contains('Login').click()
        cy.url().should('contain', '/')
        cy.contains('Trello Cards').click()
        cy.url().should('contain', '/resources/trello-cards')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.flex.items-center.py-3.border-b.border-50 > div.flex.items-center.ml-auto.px-3 > div > div > button > div').click()
        cy.get('select.block.w-full.form-control-sm.form-select').eq(5).select('100')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.flex.items-center.py-3.border-b.border-50 > div.flex.items-center.ml-auto.px-3 > div > div > button > div').click()

        cy.wait(1000)
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(6) > div > span').each(($e, index, $list) => {
            const text = $e.text()

            assert.strictEqual(text,text, 'ok')

        })
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.bg-20.rounded-b > nav > button.btn.btn-link.py-3.px-4.text-primary.dim').contains('Next').click()
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(6) > div > span').each(($e, index, $list) => {
            const text = $e.text()

            assert.strictEqual(text,text, 'ok')

        })
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.bg-20.rounded-b > nav > button.btn.btn-link.py-3.px-4.text-primary.dim').contains('Next').click()
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(6) > div > span').each(($e, index, $list) => {
            const text = $e.text()

            assert.strictEqual(text,text, 'ok')

        })

        cy.get('span.text-90').click()
        cy.contains('Logout').click()

    })




})
