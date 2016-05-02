/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.xml.bind.annotation.XmlRootElement;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "user_belong_group")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "UserBelongGroup.findAll", query = "SELECT u FROM UserBelongGroup u"),
    @NamedQuery(name = "UserBelongGroup.findByUserBelongGroupId", query = "SELECT u FROM UserBelongGroup u WHERE u.userBelongGroupId = :userBelongGroupId")})
public class UserBelongGroup implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "user_belong_group_id")
    private Long userBelongGroupId;
    @JoinColumn(name = "group_tar_id", referencedColumnName = "group_id")
    @ManyToOne(optional = false)
    private GroupInfo groupTarId;
    @JoinColumn(name = "user_src_id", referencedColumnName = "user_id")
    @ManyToOne(optional = false)
    private User userSrcId;

    public UserBelongGroup() {
    }

    public UserBelongGroup(Long userBelongGroupId) {
        this.userBelongGroupId = userBelongGroupId;
    }

    public Long getUserBelongGroupId() {
        return userBelongGroupId;
    }

    public void setUserBelongGroupId(Long userBelongGroupId) {
        this.userBelongGroupId = userBelongGroupId;
    }

    public GroupInfo getGroupTarId() {
        return groupTarId;
    }

    public void setGroupTarId(GroupInfo groupTarId) {
        this.groupTarId = groupTarId;
    }

    public User getUserSrcId() {
        return userSrcId;
    }

    public void setUserSrcId(User userSrcId) {
        this.userSrcId = userSrcId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (userBelongGroupId != null ? userBelongGroupId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof UserBelongGroup)) {
            return false;
        }
        UserBelongGroup other = (UserBelongGroup) object;
        if ((this.userBelongGroupId == null && other.userBelongGroupId != null) || (this.userBelongGroupId != null && !this.userBelongGroupId.equals(other.userBelongGroupId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.UserBelongGroup[ userBelongGroupId=" + userBelongGroupId + " ]";
    }
    
}
