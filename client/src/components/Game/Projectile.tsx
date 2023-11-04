import { BallCollider, RapierRigidBody, RigidBody } from "@react-three/rapier";
import { useEffect, useRef, useState } from "react";
import { Vector3 } from "three";
import { Texture } from "three/src/textures/Texture";

interface IProjectiileProps {
    initialSpeed: number;
    direction: Vector3;
    initialPosition: Vector3;
    texture: Texture;
    //damage
}

const Projectile = ({ initialSpeed, direction, initialPosition, texture }: IProjectiileProps) => {
    const bulletRef = useRef<RapierRigidBody>(null!);
    const [isActive, setActive] = useState<boolean>(true);

    useEffect(() => {
        direction.setLength(initialSpeed)
        bulletRef.current.setLinvel(direction, true);
    }, []);

    return (
        <RigidBody
            ref={bulletRef}
            lockRotations
            angularDamping={1}
            position={initialPosition}
            ccd
            restitution={0}
            userData={{
                type: 'projectile',
                damage: 10
            }}>
            {isActive ? <group>
                <BallCollider
                    args={[0.1]}
                    restitution={0}
                    sensor
                    onIntersectionEnter={(e) => {
                        const data: any = e.other.rigidBody?.userData;
                        if (data.type === "player" || data.type === "Collider") {
                            // bulletRef.current.setLinvel(new Vector3(), true);
                            bulletRef.current.sleep();
                            setActive(false);
                        }
                    }} />
                <sprite scale={0.5}>
                    <spriteMaterial map={texture} />
                </sprite>
            </group> : <></>}
        </RigidBody>
    );
}

export default Projectile;