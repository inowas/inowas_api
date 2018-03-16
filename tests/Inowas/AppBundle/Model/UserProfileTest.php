<?php
namespace Tests\Inowas\AppBundle\Model;


use Inowas\AppBundle\Model\UserProfile;

class UserProfileTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function it_creates_user_profile_from_array(): void
    {
        $name = 'the_name';
        $institution = 'inst';
        $email = 'email';

        $arr = [
            'name' => $name,
            'institution' => $institution,
            'email' => $email
        ];

        $userProfile = UserProfile::fromArray($arr);
        $this->assertInstanceOf(UserProfile::class, $userProfile);
        $this->assertEquals($name, $userProfile->name());
        $this->assertEquals($institution, $userProfile->institution());
        $this->assertEquals($email, $userProfile->email());
    }
}
