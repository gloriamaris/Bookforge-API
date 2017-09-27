import React from 'react'
import { Menu, Container, Image, Dropdown, Button } from 'semantic-ui-react'
import { Link } from 'react-router'

/**
 * Common header for landing pages
 * @author nikki <monique@adazing.com>
 */

class LandingHeader extends React.Component {

	constructor (props) {
		super(props)

		this.state  = {
			activeItem: null
		}
	}

	handleItemClick = (e, { name }) => this.setState({ activeItem: name })

  render () {
  	const { activeItem } = this.state

    return (
      <Menu>
        <Menu.Item header><Image src='/assets/images/bookforge-title.png' size='small'/></Menu.Item>
        <Menu.Item name='Home' active={activeItem === 'Home'} onClick={this.handleItemClick} />
        <Menu.Item name='About Us' active={activeItem === 'About Us'} onClick={this.handleItemClick} />
        <Menu.Item name='Blog' active={activeItem === 'Blog'} onClick={this.handleItemClick} />
        <Dropdown item text='Software'>
		      <Dropdown.Menu>
		        <Dropdown.Item active={activeItem === 'Software'}>Book Cover Creator</Dropdown.Item>
		        <Dropdown.Item active={activeItem === 'Software'}>Book Editor</Dropdown.Item>
		      </Dropdown.Menu>
		    </Dropdown>
        <Menu.Item name='Pricing' active={activeItem === 'Pricing'} onClick={this.handleItemClick} />

		    <Menu.Item position='right'>
          <Button color='brown'>Sign Up</Button>
        </Menu.Item>
		    <Menu.Item as='a' name='Login' className='signup-btn'/>
      </Menu>
    )
  }
}

export default LandingHeader
