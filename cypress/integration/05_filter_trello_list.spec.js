describe('Not create Card', () => {

    it('Not create Card', () => {
        cy.visit('/nova/login')
        cy.get('input[name=email]').type('alessiopiccioli@webmapp.it')
        cy.get('input[name=password]').type('webmapp2020')
        cy.get('button').contains('Login').click()
        cy.url().should('contain', '/')

        cy.contains('Trello Card').click()
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.flex.items-center.py-3.border-b.border-50 > div.flex.items-center.ml-auto.px-3 > div > div > button > div').click()
        cy.get('select.block.w-full.form-control-sm.form-select').eq(0).select('Done')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(4) > div > span > span').each(($e, index, $list) => {
            const text = $e.text()

                expect(text).to.eq('DONE')

        })

        cy.get('select.block.w-full.form-control-sm.form-select').eq(0).select('To be Tested')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(4) > div > span > span').each(($e, index, $list) => {
            const text = $e.text()

            expect(text).to.eq('TO BE TESTED')


        })

        cy.get('select.block.w-full.form-control-sm.form-select').eq(0).select('Almost there')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(4) > div > span > span').each(($e, index, $list) => {
            const text = $e.text()

            expect(text).to.eq('ALMOST THERE')


        })

        cy.get('select.block.w-full.form-control-sm.form-select').eq(0).select('Progress')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(4) > div > span > span').each(($e, index, $list) => {
            const text = $e.text()
            expect(text).to.eq('PROGRESS')

        })

        cy.get('select.block.w-full.form-control-sm.form-select').eq(0).select('Today')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(4) > div > span > span').each(($e, index, $list) => {
            const text = $e.text()
            expect(text).to.eq('TODAY')

        })

        cy.get('select.block.w-full.form-control-sm.form-select').eq(0).select('Tomorrow')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(4) > div > span > span').each(($e, index, $list) => {
            const text = $e.text()
            expect(text).to.eq('TOMORROW')

        })

        cy.get('select.block.w-full.form-control-sm.form-select').eq(0).select('After Tomorrow')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(4) > div > span > span').each(($e, index, $list) => {
            const text = $e.text()
            expect(text).to.eq('AFTER TOMORROW')

        })

        cy.get('select.block.w-full.form-control-sm.form-select').eq(0).select('After After Tomorrow')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(4) > div > span > span').each(($e, index, $list) => {
            const text = $e.text()
            expect(text).to.eq('AFTER AFTER TOMORROW')

        })

        cy.get('select.block.w-full.form-control-sm.form-select').eq(0).select('New')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(4) > div > span > span').each(($e, index, $list) => {
            const text = $e.text()
            expect(text).to.eq('NEW')

        })

        cy.get('select.block.w-full.form-control-sm.form-select').eq(0).select('Backlog')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(4) > div > span > span').each(($e, index, $list) => {
            const text = $e.text()
            expect(text).to.eq('BACKLOG')

        })

        cy.get('select.block.w-full.form-control-sm.form-select').eq(0).select('Cyclando Optimize')
        cy.get('#nova > div > div.content > div.px-view.py-view.mx-auto > div.relative > div.card > div.relative > div.overflow-hidden.overflow-x-auto.relative > table > tbody > tr > td:nth-child(4) > div > span > span').each(($e, index, $list) => {
            const text = $e.text()
            expect(text).to.eq('CYCLANDO OPTIMIZE')

        })


        cy.get('span.text-90').click()
        cy.contains('Logout').click()



    })




})
