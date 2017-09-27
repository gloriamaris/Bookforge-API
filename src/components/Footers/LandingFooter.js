import React from 'react'
import { Segment, Header, List } from 'semantic-ui-react'
import { Link } from 'react-router'

/**
 * Common footer for landing pages
 * @author nikki <monique.dingding@softwarewot.com>
 */

class LandingFooter extends React.Component {
  render () {
    return (
      <Segment textAlign='center' basic>
        <Header as='h4' color='grey'>Copyright ©2017 Adazing Designs</Header>
        <p className='footer'>Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim</p>
        <List horizontal divided>
          <List.Item as={Link} to='/privacy-policy'>Privacy Policy</List.Item>
          <List.Item as={Link} to='/terms-of-use'>Terms of Use</List.Item>
        </List>
      </Segment>
    )
  }
}

export default LandingFooter
